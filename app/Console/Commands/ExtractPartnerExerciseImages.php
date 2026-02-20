<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use App\Models\Partner;
use App\Services\PartnerExerciseFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ExtractPartnerExerciseImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:images:extract {partner} {--time=2 : Time in seconds to extract frame from (default: 2)} {--force : Overwrite existing images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract thumbnail images from partner exercise videos using FFmpeg';

    /**
     * Execute the console command.
     */
    public function handle(PartnerExerciseFileService $fileService): int
    {
        $partnerIdentifier = $this->argument('partner');
        $extractTime = (float) $this->option('time');
        $force = $this->option('force');

        // Find partner by slug or ID
        $partner = is_numeric($partnerIdentifier)
            ? Partner::find($partnerIdentifier)
            : Partner::where('slug', $partnerIdentifier)->first();

        if (! $partner) {
            $this->error("Partner not found: {$partnerIdentifier}");

            return Command::FAILURE;
        }

        // Check if FFmpeg is available
        if (! $this->checkFFmpegAvailable()) {
            $this->error('FFmpeg is not installed or not available in PATH. Please install FFmpeg first.');

            return Command::FAILURE;
        }

        $this->info("Extracting images for partner: {$partner->name} ({$partner->slug})");
        $this->info("Extract time: {$extractTime} seconds");
        $this->info('Force overwrite: '.($force ? 'Yes' : 'No'));
        $this->newLine();

        // Find exercises with videos but no images
        $exercises = $this->findExercisesWithVideosButNoImages($partner, $force);

        if ($exercises->isEmpty()) {
            $this->warn('No exercises found with videos but missing images.');

            return Command::SUCCESS;
        }

        $this->info('Found '.$exercises->count().' exercise(s) to process.');
        $this->newLine();

        // Process exercises
        $processed = 0;
        $extracted = 0;
        $failed = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($exercises->count());
        $bar->start();

        foreach ($exercises as $exercise) {
            try {
                $processed++;

                // Get video path from pivot
                $videoPath = $exercise->pivot->video;
                if (! $videoPath) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                // Check if video exists in storage
                if (! Storage::exists($videoPath)) {
                    $this->newLine();
                    $this->warn("  Video not found: {$videoPath} (Exercise: {$exercise->name})");
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                // Extract and store image
                $imagePath = $this->extractImageFromVideo($fileService, $videoPath, $partner, $exercise, $extractTime);

                if ($imagePath) {
                    // Update pivot table with image path
                    $partner->exercises()->updateExistingPivot($exercise->id, [
                        'image' => $imagePath,
                    ]);
                    $extracted++;

                    // Log the exercise being updated
                    Log::info('[ExtractPartnerExerciseImages] Updated exercise with image', [
                        'partner' => $partner->slug,
                        'partner_id' => $partner->id,
                        'exercise_id' => $exercise->id,
                        'exercise_name' => $exercise->name,
                        'image_path' => $imagePath,
                        'video_path' => $videoPath,
                    ]);
                } else {
                    $failed++;
                    $this->newLine();
                    $this->warn("  Failed to extract image: {$exercise->name}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("  Error processing {$exercise->name}: {$e->getMessage()}");
                Log::error('[ExtractPartnerExerciseImages] Error processing exercise', [
                    'exercise_id' => $exercise->id,
                    'exercise_name' => $exercise->name,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Output summary
        $this->info('Summary:');
        $this->line("  Processed: {$processed}");
        $this->line("  Extracted: {$extracted}");
        $this->line("  Skipped: {$skipped}");
        $this->line("  Failed: {$failed}");

        return Command::SUCCESS;
    }

    /**
     * Check if FFmpeg is available
     */
    private function checkFFmpegAvailable(): bool
    {
        try {
            $result = Process::run('ffmpeg -version');

            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find exercises with videos but no images
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function findExercisesWithVideosButNoImages(Partner $partner, bool $force)
    {

        $query = $partner->exercises()
            ->wherePivot('video', '!=', null)
            ->wherePivot('image', null);


        return $query->get();
    }

    /**
     * Extract image from video using FFmpeg
     */
    private function extractImageFromVideo(
        PartnerExerciseFileService $fileService,
        string $videoPath,
        Partner $partner,
        Exercise $exercise,
        float $extractTime
    ): ?string {
        $tempImagePath = null;

        try {
            // Get target image path
            $targetImagePath = $fileService->getImagePath($partner, $exercise, 'jpg');

            // Delete old image if force is enabled
            if ($this->option('force')) {
                $fileService->deleteImage($partner, $exercise);
            }

            // Check if image already exists
            if (! $this->option('force') && Storage::exists($targetImagePath)) {
                return $targetImagePath;
            }

            // Get video path for FFmpeg (URL for S3, local path for local storage)
            $defaultDisk = config('filesystems.default');
            $isRemoteStorage = in_array($defaultDisk, ['s3']);

            if ($isRemoteStorage) {
                // Generate a temporary signed URL for FFmpeg to read directly from S3
                $tempVideoPath = Storage::temporaryUrl($videoPath, now()->addMinutes(5));
            } else {
                // Use storage path directly for local files
                $storagePath = Storage::path($videoPath);
                if (! file_exists($storagePath)) {
                    Log::error('[ExtractPartnerExerciseImages] Video file does not exist', [
                        'path' => $storagePath,
                        'storage_path' => $videoPath,
                    ]);

                    return null;
                }
                $tempVideoPath = $storagePath;
            }

            // Create temp file for extracted image
            $tempImagePath = sys_get_temp_dir().'/'.uniqid('image_', true).'.jpg';

            // Format time as HH:MM:SS
            $hours = floor($extractTime / 3600);
            $minutes = floor(($extractTime % 3600) / 60);
            $seconds = $extractTime % 60;
            $timeString = sprintf('%02d:%02d:%05.2f', $hours, $minutes, $seconds);

            // Run FFmpeg command
            $command = sprintf(
                'ffmpeg -i %s -ss %s -vframes 1 -q:v 2 -y %s 2>&1',
                escapeshellarg($tempVideoPath),
                escapeshellarg($timeString),
                escapeshellarg($tempImagePath)
            );

            $result = Process::run($command);

            if (! $result->successful() || ! file_exists($tempImagePath)) {
                Log::error('[ExtractPartnerExerciseImages] FFmpeg extraction failed', [
                    'command' => $command,
                    'output' => $result->output(),
                    'error' => $result->errorOutput(),
                    'video_path' => $videoPath,
                    'exercise' => $exercise->name,
                ]);

                return null;
            }

            // Read extracted image and store it
            $imageContents = file_get_contents($tempImagePath);
            if ($imageContents === false) {
                Log::error('[ExtractPartnerExerciseImages] Failed to read extracted image', [
                    'temp_path' => $tempImagePath,
                ]);

                return null;
            }

            // Store the image
            $stored = Storage::put($targetImagePath, $imageContents);

            if (! $stored) {
                Log::error('[ExtractPartnerExerciseImages] Failed to store image', [
                    'target' => $targetImagePath,
                ]);

                return null;
            }

            Log::info('[ExtractPartnerExerciseImages] Image extracted successfully', [
                'video_path' => $videoPath,
                'image_path' => $targetImagePath,
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
                'extract_time' => $extractTime,
            ]);

            return $targetImagePath;
        } catch (\Exception $e) {
            Log::error('[ExtractPartnerExerciseImages] Error extracting image', [
                'video_path' => $videoPath,
                'error' => $e->getMessage(),
                'partner' => $partner->slug,
                'exercise' => $exercise->name,
            ]);

            return null;
        } finally {
            // Clean up temp image file
            if ($tempImagePath && file_exists($tempImagePath)) {
                @unlink($tempImagePath);
            }
            // No need to clean up video - we used a URL or local path directly
        }
    }
}
