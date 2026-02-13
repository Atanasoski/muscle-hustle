<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Partner Exercise File Service
 * Handles deterministic file naming for partner exercise images and videos
 * to preserve files across database migrations
 */
class PartnerExerciseFileService
{
    /**
     * Get the deterministic filename for a partner exercise image
     *
     * @return string Filename without extension
     */
    public function getImageFilename(Partner $partner, Exercise $exercise): string
    {
        // Use only stable identifiers: slug + name (NOT id)
        $hash = md5($partner->slug.'-'.$exercise->name);

        return "partner-{$partner->slug}-exercise-".Str::slug($exercise->name)."-image-{$hash}";
    }

    /**
     * Get the deterministic filename for a partner exercise video
     *
     * @return string Filename without extension
     */
    public function getVideoFilename(Partner $partner, Exercise $exercise): string
    {
        // Use only stable identifiers: slug + name (NOT id)
        $hash = md5($partner->slug.'-'.$exercise->name);

        return "partner-{$partner->slug}-exercise-".Str::slug($exercise->name)."-video-{$hash}";
    }

    /**
     * Get the storage path for a partner exercise image
     *
     * @return string Full storage path
     */
    public function getImagePath(Partner $partner, Exercise $exercise, string $extension = 'jpg'): string
    {
        $filename = $this->getImageFilename($partner, $exercise);

        return "{$partner->slug}/exercises/images/{$filename}.{$extension}";
    }

    /**
     * Get the storage path for a partner exercise video
     *
     * @return string Full storage path
     */
    public function getVideoPath(Partner $partner, Exercise $exercise, string $extension = 'mp4'): string
    {
        $filename = $this->getVideoFilename($partner, $exercise);

        return "{$partner->slug}/exercises/videos/{$filename}.{$extension}";
    }

    /**
     * Store an image file for a partner exercise
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string|null Storage path or null if failed
     */
    public function storeImage($file, Partner $partner, Exercise $exercise): ?string
    {
        try {
            $filename = $this->getImageFilename($partner, $exercise);
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
            $path = "{$partner->slug}/exercises/images/{$filename}.{$extension}";

            // Delete old file if it exists (different extension)
            $this->deleteOldImage($partner, $exercise);

            Storage::putFileAs(
                "{$partner->slug}/exercises/images",
                $file,
                "{$filename}.{$extension}"
            );

            Log::info('[PartnerExerciseFileService] Image stored successfully', [
                'path' => $path,
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('[PartnerExerciseFileService] Error storing image', [
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return null;
        }
    }

    /**
     * Store a video file for a partner exercise
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string|null Storage path or null if failed
     */
    public function storeVideo($file, Partner $partner, Exercise $exercise): ?string
    {
        try {
            $filename = $this->getVideoFilename($partner, $exercise);
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'mp4';
            $path = "{$partner->slug}/exercises/videos/{$filename}.{$extension}";

            // Delete old file if it exists (different extension)
            $this->deleteOldVideo($partner, $exercise);

            Storage::putFileAs(
                "{$partner->slug}/exercises/videos",
                $file,
                "{$filename}.{$extension}"
            );

            Log::info('[PartnerExerciseFileService] Video stored successfully', [
                'path' => $path,
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('[PartnerExerciseFileService] Error storing video', [
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return null;
        }
    }

    /**
     * Check if an image exists for a partner exercise
     */
    public function imageExists(Partner $partner, Exercise $exercise): bool
    {
        $filename = $this->getImageFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/images";

        // Check for common image extensions
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($extensions as $ext) {
            if (Storage::exists("{$directory}/{$filename}.{$ext}")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a video exists for a partner exercise
     */
    public function videoExists(Partner $partner, Exercise $exercise): bool
    {
        $filename = $this->getVideoFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/videos";

        // Check for common video extensions
        $extensions = ['mp4', 'mov', 'avi', 'webm'];
        foreach ($extensions as $ext) {
            if (Storage::exists("{$directory}/{$filename}.{$ext}")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the existing image path if it exists (checks all extensions)
     *
     * @return string|null Full storage path or null if not found
     */
    public function getExistingImagePath(Partner $partner, Exercise $exercise): ?string
    {
        $filename = $this->getImageFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/images";

        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($extensions as $ext) {
            $path = "{$directory}/{$filename}.{$ext}";
            if (Storage::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get the existing video path if it exists (checks all extensions)
     *
     * @return string|null Full storage path or null if not found
     */
    public function getExistingVideoPath(Partner $partner, Exercise $exercise): ?string
    {
        $filename = $this->getVideoFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/videos";

        $extensions = ['mp4', 'mov', 'avi', 'webm'];
        foreach ($extensions as $ext) {
            $path = "{$directory}/{$filename}.{$ext}";
            if (Storage::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Delete old image files for a partner exercise (handles different extensions)
     */
    private function deleteOldImage(Partner $partner, Exercise $exercise): void
    {
        $filename = $this->getImageFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/images";

        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($extensions as $ext) {
            $path = "{$directory}/{$filename}.{$ext}";
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
    }

    /**
     * Delete old video files for a partner exercise (handles different extensions)
     */
    private function deleteOldVideo(Partner $partner, Exercise $exercise): void
    {
        $filename = $this->getVideoFilename($partner, $exercise);
        $directory = "{$partner->slug}/exercises/videos";

        $extensions = ['mp4', 'mov', 'avi', 'webm'];
        foreach ($extensions as $ext) {
            $path = "{$directory}/{$filename}.{$ext}";
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
    }

    /**
     * Delete image file for a partner exercise
     */
    public function deleteImage(Partner $partner, Exercise $exercise): bool
    {
        try {
            $this->deleteOldImage($partner, $exercise);

            return true;
        } catch (\Exception $e) {
            Log::error('[PartnerExerciseFileService] Error deleting image', [
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return false;
        }
    }

    /**
     * Delete video file for a partner exercise
     */
    public function deleteVideo(Partner $partner, Exercise $exercise): bool
    {
        try {
            $this->deleteOldVideo($partner, $exercise);

            return true;
        } catch (\Exception $e) {
            Log::error('[PartnerExerciseFileService] Error deleting video', [
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return false;
        }
    }
}
