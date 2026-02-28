<?php

namespace App\Services;

use App\Models\Partner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Plan File Service
 * Handles plan-related file storage (cover images, thumbnails, etc.) using the default disk.
 * Partner library plans use partner-scoped paths; user plans use a flat path.
 */
class PlanFileService
{
    public const TYPE_COVER = 'cover-images';

    /**
     * Allowed path segment types (whitelist for storePlanFile).
     *
     * @var array<string>
     */
    private const ALLOWED_TYPES = [
        self::TYPE_COVER,
    ];

    /**
     * Store a plan file. Uses the default disk (same as PartnerExerciseFileService).
     *
     * @param  string  $type  Path segment, e.g. TYPE_COVER. Must be in ALLOWED_TYPES.
     * @return string Storage path
     */
    public function storePlanFile(UploadedFile $file, string $type, ?Partner $partner = null): string
    {
        if (! in_array($type, self::ALLOWED_TYPES, true)) {
            throw new \InvalidArgumentException("Invalid plan file type: {$type}");
        }

        $extension = $file->getClientOriginalExtension()
            ?: $file->guessExtension()
            ?: 'jpg';
        $filename = Str::random(40).'.'.strtolower($extension);

        $directory = $partner
            ? "{$partner->slug}/plans/{$type}"
            : "plans/{$type}";

        Storage::putFileAs($directory, $file, $filename);

        return "{$directory}/{$filename}";
    }

    /**
     * Delete a plan file from the default disk.
     */
    public function deletePlanFile(?string $path): void
    {
        if ($path) {
            Storage::delete($path);
        }
    }

    /**
     * Store a cover image file (convenience wrapper).
     *
     * @return string Storage path
     */
    public function storeCoverImage(UploadedFile $file, ?Partner $partner = null): string
    {
        return $this->storePlanFile($file, self::TYPE_COVER, $partner);
    }

    /**
     * Delete a cover image file (convenience wrapper).
     */
    public function deleteCoverImage(?string $path): void
    {
        $this->deletePlanFile($path);
    }
}
