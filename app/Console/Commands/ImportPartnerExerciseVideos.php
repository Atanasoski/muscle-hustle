<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use App\Models\Partner;
use App\Services\PartnerExerciseFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportPartnerExerciseVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:videos:import {folder} {partner} {--source-disk= : Optional: source disk if different from default. Absolute paths (/) use local filesystem automatically}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import video files from a folder (local or storage) and assign them to partner exercises';

    /**
     * Execute the console command.
     */
    public function handle(PartnerExerciseFileService $fileService): int
    {
        $folder = $this->argument('folder');
        $partnerIdentifier = $this->argument('partner');
        $sourceDisk = $this->option('source-disk');

        // Find partner by slug or ID
        $partner = is_numeric($partnerIdentifier)
            ? Partner::find($partnerIdentifier)
            : Partner::where('slug', $partnerIdentifier)->first();

        if (! $partner) {
            $this->error("Partner not found: {$partnerIdentifier}");

            return Command::FAILURE;
        }

        $defaultDisk = config('filesystems.default');

        // Determine source: check if it's a valid absolute filesystem path
        // If path starts with / but doesn't exist as absolute path, treat as storage path
        $isLocalPath = str_starts_with($folder, '/') && (is_dir($folder) || is_file($folder));
        $actualSourceDisk = $sourceDisk ?: ($isLocalPath ? null : $defaultDisk);

        $this->info("Importing videos for partner: {$partner->name} ({$partner->slug})");
        $this->info("Source folder: {$folder}");
        $this->info('Source: '.($isLocalPath ? 'local filesystem' : "disk '{$actualSourceDisk}'"));
        $this->info("Destination: disk '{$defaultDisk}'");
        $this->newLine();

        // Scan for video files
        if ($isLocalPath) {
            $videoFiles = $this->scanForVideoFiles($folder);
        } else {
            $videoFiles = $this->scanForVideoFilesFromDisk($folder, $actualSourceDisk);
        }

        if (empty($videoFiles)) {
            $this->warn('No video files found in the specified folder.');

            return Command::SUCCESS;
        }

        $this->info('Found '.count($videoFiles).' video file(s) to process.');
        $this->newLine();

        // Get all exercises for matching
        $exercises = Exercise::all();
        $this->info('Loaded '.$exercises->count().' exercises for matching.');
        $this->newLine();

        // Process videos
        $matched = 0;
        $linked = 0;
        $moved = 0;
        $failed = 0;
        $unmatched = [];

        $bar = $this->output->createProgressBar(count($videoFiles));
        $bar->start();

        foreach ($videoFiles as $videoPath) {
            try {
                // Get filename from path (works for both absolute paths and storage paths)
                $filename = basename($videoPath);
                $exerciseName = $this->extractExerciseName($filename);

                // Find matching exercise
                $exercise = $this->findMatchingExercise($exerciseName, $exercises);

                if (! $exercise) {
                    $unmatched[] = $filename;
                    $bar->advance();

                    continue;
                }

                $matched++;

                // Link exercise to partner if not already linked
                $wasLinked = $partner->exercises()->where('workout_exercises.id', $exercise->id)->exists();
                if (! $wasLinked) {
                    $partner->exercises()->syncWithoutDetaching([
                        $exercise->id => [
                            'description' => null,
                            'image' => null,
                            'video' => null,
                        ],
                    ]);
                    $linked++;
                }

                // Move video file
                $extension = $this->getFileExtension($videoPath);
                $targetPath = $this->moveVideoFile($fileService, $videoPath, $partner, $exercise, $extension, $isLocalPath ? null : $actualSourceDisk);

                if ($targetPath) {
                    // Update pivot table with video path
                    $partner->exercises()->updateExistingPivot($exercise->id, [
                        'video' => $targetPath,
                    ]);
                    $moved++;
                } else {
                    $failed++;
                    $this->newLine();
                    $this->warn("  Failed to move video: {$filename}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("  Error processing {$filename}: {$e->getMessage()}");
                Log::error('[ImportPartnerExerciseVideos] Error processing video', [
                    'file' => $videoPath,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Output summary
        $this->info('Summary:');
        $this->line("  Matched: {$matched}");
        $this->line("  Linked: {$linked}");
        $this->line("  Moved: {$moved}");
        $this->line("  Failed: {$failed}");

        if (! empty($unmatched)) {
            $this->newLine();
            $this->warn('Unmatched videos ('.count($unmatched).'):');
            foreach ($unmatched as $filename) {
                $this->line("  - {$filename}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Scan folder for video files (local filesystem)
     *
     * @return array<string>
     */
    private function scanForVideoFiles(string $folder): array
    {
        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
        $files = [];

        if (! is_dir($folder)) {
            $this->error("Folder does not exist: {$folder}");

            return $files;
        }

        if (! is_readable($folder)) {
            $this->error("Folder is not readable: {$folder}");

            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, $videoExtensions)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }

    /**
     * Scan folder for video files from a storage disk (S3, etc.)
     *
     * @return array<string> Array of file paths relative to the disk
     */
    private function scanForVideoFilesFromDisk(string $folder, string $disk): array
    {
        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
        $files = [];

        try {
            $storage = Storage::disk($disk);

            // Ensure folder path doesn't have trailing slash
            $folder = rtrim($folder, '/');

            if (! $storage->exists($folder)) {
                $this->error("Folder does not exist on disk '{$disk}': {$folder}");

                return $files;
            }

            // Get all files recursively
            $allFiles = $storage->allFiles($folder);

            foreach ($allFiles as $filePath) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($extension, $videoExtensions)) {
                    $files[] = $filePath;
                }
            }
        } catch (\Exception $e) {
            $this->error("Error scanning disk '{$disk}': {$e->getMessage()}");

            return $files;
        }

        return $files;
    }

    /**
     * Get file extension from path
     */
    private function getFileExtension(string $filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return $extension ?: 'mp4';
    }

    /**
     * Extract exercise name from filename
     */
    private function extractExerciseName(string $filename): string
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Normalize to slug for matching
        return Str::slug($name);
    }

    /**
     * Find matching exercise using fuzzy matching
     */
    private function findMatchingExercise(string $normalizedName, $exercises): ?Exercise
    {
        // First try exact match (case-insensitive)
        $exercise = $exercises->first(function ($exercise) use ($normalizedName) {
            return Str::slug($exercise->name) === $normalizedName;
        });

        if ($exercise) {
            return $exercise;
        }

        // Then try fuzzy matching - check if normalized name contains exercise name or vice versa
        $exercise = $exercises->first(function ($exercise) use ($normalizedName) {
            $exerciseSlug = Str::slug($exercise->name);

            return str_contains($normalizedName, $exerciseSlug) || str_contains($exerciseSlug, $normalizedName);
        });

        if ($exercise) {
            return $exercise;
        }

        // Try similarity matching (Levenshtein distance)
        $bestMatch = null;
        $bestScore = PHP_INT_MAX;

        foreach ($exercises as $ex) {
            $exerciseSlug = Str::slug($ex->name);
            $distance = levenshtein($normalizedName, $exerciseSlug);

            // If very similar (within 3 character difference for short names, or percentage for longer)
            $maxDistance = min(3, max(1, (int) (strlen($normalizedName) * 0.2)));
            if ($distance <= $maxDistance && $distance < $bestScore) {
                $bestScore = $distance;
                $bestMatch = $ex;
            }
        }

        return $bestMatch;
    }

    /**
     * Move video file to partner's storage directory
     */
    private function moveVideoFile(
        PartnerExerciseFileService $fileService,
        string $sourcePath,
        Partner $partner,
        Exercise $exercise,
        string $extension,
        ?string $sourceDisk = null
    ): ?string {
        try {
            // Get target path using the service (will use default disk)
            $targetPath = $fileService->getVideoPath($partner, $exercise, $extension);

            // Delete old video if exists (different extension)
            $fileService->deleteVideo($partner, $exercise);

            // Read source file
            $fileContents = null;
            if ($sourceDisk) {
                // Read from storage disk (S3, etc.)
                $sourceStorage = Storage::disk($sourceDisk);
                if (! $sourceStorage->exists($sourcePath)) {
                    Log::error('[ImportPartnerExerciseVideos] Source file does not exist on disk', [
                        'source' => $sourcePath,
                        'disk' => $sourceDisk,
                    ]);

                    return null;
                }
                $fileContents = $sourceStorage->get($sourcePath);
            } else {
                // Read from local filesystem
                $fileContents = file_get_contents($sourcePath);
                if ($fileContents === false) {
                    Log::error('[ImportPartnerExerciseVideos] Failed to read source file', [
                        'source' => $sourcePath,
                    ]);

                    return null;
                }
            }

            // Store the file to default disk (could be local or S3)
            $stored = Storage::put($targetPath, $fileContents);

            if (! $stored) {
                Log::error('[ImportPartnerExerciseVideos] Failed to store video', [
                    'target' => $targetPath,
                ]);

                return null;
            }

            // Optionally delete source file if it's on a storage disk (not local filesystem)
            // Uncomment if you want to delete source files after successful import
            // if ($sourceDisk) {
            //     Storage::disk($sourceDisk)->delete($sourcePath);
            // }

            Log::info('[ImportPartnerExerciseVideos] Video moved successfully', [
                'source' => $sourcePath,
                'source_disk' => $sourceDisk ?? 'local',
                'target' => $targetPath,
                'target_disk' => config('filesystems.default'),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return $targetPath;
        } catch (\Exception $e) {
            Log::error('[ImportPartnerExerciseVideos] Error moving video file', [
                'source' => $sourcePath,
                'source_disk' => $sourceDisk ?? 'local',
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return null;
        }
    }
}
