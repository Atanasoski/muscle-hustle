import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';

FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
);

const INIT_ATTR = 'data-filepond-initialized';

/**
 * Build options for FilePond from input data attributes.
 * @param {HTMLInputElement} input
 * @returns {Object}
 */
function getOptionsFromInput(input) {
    const accept = (input.getAttribute('data-accept') || input.getAttribute('accept') || '').trim();
    const acceptedFileTypes = accept ? accept.split(',').map((t) => t.trim()).filter(Boolean) : null;
    const maxFileSize = input.getAttribute('data-max-file-size') || '5MB';
    const allowMultiple = input.getAttribute('data-allow-multiple') === 'true';
    // Crop disabled for now (keep compression via image-transform)
    // const allowCrop = input.getAttribute('data-allow-crop') === 'true';
    // const cropAspectRatio = input.getAttribute('data-crop-aspect-ratio')?.trim() || null;
    const allowResize = input.getAttribute('data-allow-resize') !== 'false';
    const resizeTargetWidth = input.getAttribute('data-resize-target-width');
    const resizeTargetHeight = input.getAttribute('data-resize-target-height');
    const resizeMode = input.getAttribute('data-resize-mode')?.trim() || 'cover';
    const name = input.getAttribute('name') || 'file';

    const options = {
        name,
        acceptedFileTypes,
        maxFileSize: maxFileSize || undefined,
        allowMultiple,
        allowImageCrop: false, // crop disabled for now (was: allowCrop / imageCropAspectRatio)
        allowImageResize: allowResize && (resizeTargetWidth || resizeTargetHeight),
        imageResizeTargetWidth: resizeTargetWidth ? parseInt(resizeTargetWidth, 10) : null,
        imageResizeTargetHeight: resizeTargetHeight ? parseInt(resizeTargetHeight, 10) : null,
        imageResizeMode: resizeMode || undefined,
        imageResizeUpscale: false,
        // Limit canvas size so large images are downscaled before crop/resize (avoids hang/slow transform)
        imageTransformCanvasMemoryLimit: 2560 * 1440,
        // Compression via image-transform (free plugin): output as JPEG with quality 0.85
        imageTransformOutputMimeType: 'image/jpeg',
        imageTransformOutputQuality: 0.85,
    };

    return options;
}

const PREPARE_TIMEOUT_MS = 25000;

/**
 * Get the prepared (e.g. transformed/cropped/resized) file for a FilePond item.
 * Uses requestPrepare() if available; on failure, timeout, or missing result, falls back to item.file.
 * @param {Object} item - FilePond file item
 * @returns {Promise<File|null>}
 */
function getPreparedFile(item) {
    if (!item.file) return Promise.resolve(null);
    if (typeof item.requestPrepare !== 'function') return Promise.resolve(item.file);

    const preparePromise = item.requestPrepare().then((result) => {
        const file = result && (result.output instanceof File ? result.output : result.file);
        return file instanceof File ? file : item.file;
    }).catch(() => item.file);

    const timeoutPromise = new Promise((resolve) => {
        setTimeout(() => resolve(item.file), PREPARE_TIMEOUT_MS);
    });

    return Promise.race([preparePromise, timeoutPromise]).then((file) => file || item.file);
}

/**
 * Append file inputs to the form (removes existing same-name inputs first).
 * @param {HTMLFormElement} form
 * @param {Array<{ name: string, allowMultiple: boolean, files: File[] }>} entries
 */
function appendFileInputsToForm(form, entries) {
    entries.forEach(({ name, allowMultiple, files }) => {
        const fileObjects = files.filter(Boolean);
        if (fileObjects.length === 0) return;

        const existing = form.querySelectorAll(`input[name="${name}"]`);
        existing.forEach((el) => el.remove());

        const dt = new DataTransfer();
        fileObjects.forEach((f) => dt.items.add(f));
        const input = document.createElement('input');
        input.type = 'file';
        input.name = name;
        input.multiple = allowMultiple;
        input.files = dt.files;
        input.style.display = 'none';
        form.appendChild(input);
    });
}

/**
 * Sync FilePond file(s) from the pond into the form so the request includes the file(s).
 * Uses requestPrepare() when available for transformed output; falls back to item.file.
 * Removes any existing file inputs with the same name, then appends new hidden input(s).
 * @param {HTMLFormElement} form
 * @param {FilePond[]} instances
 * @returns {Promise<void>}
 */
function syncFilePondFilesToForm(form, instanceEntries) {
    const promises = [];
    instanceEntries.forEach(({ pond, options }) => {
        const name = options.name || 'file';
        const allowMultiple = options.allowMultiple || false;
        pond.getFiles().forEach((item) => {
            promises.push(getPreparedFile(item));
        });
    });

    return Promise.all(promises).then((fileArrays) => {
        let fileIndex = 0;
        const entries = instanceEntries.map(({ pond, options }) => {
            const name = options.name || 'file';
            const allowMultiple = options.allowMultiple || false;
            const fileCount = pond.getFiles().length;
            const files = fileArrays.slice(fileIndex, fileIndex + fileCount).filter(Boolean);
            fileIndex += fileCount;
            return { name, allowMultiple, files };
        });
        appendFileInputsToForm(form, entries);
    });
}

/**
 * Initialize FilePond on all input[data-filepond] inside container.
 * Also attaches submit handler to forms that contain a FilePond so files are synced before submit.
 * @param {Document|Element} [container=document]
 * @returns {void}
 */
export function initFilePond(container = document) {
    const root = container instanceof Document ? document.body : container;
    const inputs = root.querySelectorAll('input[data-filepond]:not([' + INIT_ATTR + '])');

    inputs.forEach((input) => {
        if (input.hasAttribute(INIT_ATTR)) return;

        const form = input.closest('form');
        const options = getOptionsFromInput(input);
        const pond = FilePond.create(input, options);

        input.setAttribute(INIT_ATTR, 'true');

        if (form) {
            if (!form._filepondSubmitBound) {
                form._filepondSubmitBound = true;
                form.addEventListener('submit', function handleFilePondSubmit(e) {
                    if (!form._filepondEntries || form._filepondEntries.length === 0) return;
                    const hasFiles = form._filepondEntries.some(({ pond }) => pond.getFiles().some((item) => item.file));
                    if (!hasFiles) return;

                    e.preventDefault();
                    e.stopImmediatePropagation();
                    syncFilePondFilesToForm(form, form._filepondEntries).then(() => {
                        form.submit();
                    }).catch(() => {
                        const fallbackEntries = form._filepondEntries.map(({ pond, options }) => ({
                            name: options.name || 'file',
                            allowMultiple: options.allowMultiple || false,
                            files: pond.getFiles().map((item) => item.file).filter(Boolean),
                        }));
                        appendFileInputsToForm(form, fallbackEntries);
                        form.submit();
                    });
                }, true);
            }
            if (!form._filepondEntries) form._filepondEntries = [];
            form._filepondEntries.push({ pond, options });
        }
    });
}

if (typeof window !== 'undefined') {
    window.initFilePond = initFilePond;
}
