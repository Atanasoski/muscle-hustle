<?php

namespace App\Console\Commands;

use App\Models\Partner;
use App\Services\PartnerExerciseFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OptimizePartnerExerciseVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:videos:optimize {partner} {--force : Optimize videos even if they may already be optimized}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing partner exercise videos with faststart flag for web streaming';

    /**
     * Execute the console command.
     */
    public function handle(PartnerExerciseFileService $fileService): int
    {
        $partnerIdentifier = $this->argument('partner');
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

        $this->info("Optimizing videos for partner: {$partner->name} ({$partner->slug})");
        $this->info('Force re-optimize: '.($force ? 'Yes' : 'No'));
        $this->newLine();

        // Find exercises with videos
        $exercises = $partner->exercises()
            ->wherePivot('video', '!=', null)
            ->get();

        if ($exercises->isEmpty()) {
            $this->warn('No exercises found with videos.');

            return Command::SUCCESS;
        }

        $this->info('Found '.$exercises->count().' exercise(s) with videos to process.');
        $this->newLine();

        // Process videos
        $processed = 0;
        $optimized = 0;
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

                // Only optimize MP4 files
                if (pathinfo($videoPath, PATHINFO_EXTENSION) !== 'mp4') {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                // Optimize video
                $success = $fileService->optimizeVideo($videoPath);

                if ($success) {
                    $optimized++;

                    // Log the exercise being optimized
                    Log::info('[OptimizePartnerExerciseVideos] Optimized exercise video', [
                        'partner' => $partner->slug,
                        'partner_id' => $partner->id,
                        'exercise_id' => $exercise->id,
                        'exercise_name' => $exercise->name,
                        'video_path' => $videoPath,
                    ]);
                } else {
                    $failed++;
                    $this->newLine();
                    $this->warn("  Failed to optimize video: {$exercise->name}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("  Error processing {$exercise->name}: {$e->getMessage()}");
                Log::error('[OptimizePartnerExerciseVideos] Error processing exercise', [
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
        $this->line("  Optimized: {$optimized}");
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
            $result = \Illuminate\Support\Facades\Process::run('ffmpeg -version');

            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
