<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use App\Services\MuscleGroupImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateExerciseMuscleImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exercises:update-muscle-images {--force : Actually update exercises in database} {--skip-existing : Skip exercises that already have muscle group images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update muscle group images for all exercises using MuscleGroupImageService';

    private array $results = [];

    /**
     * Execute the console command.
     */
    public function handle(MuscleGroupImageService $muscleGroupImageService): int
    {
        // Check if service is configured
        if (! $muscleGroupImageService->isConfigured()) {
            $this->error('MuscleGroupImageService is not configured. Please check your RapidAPI configuration.');

            return Command::FAILURE;
        }

        $this->info('Fetching exercises...');

        // Build query based on flags
        $query = Exercise::with(['primaryMuscleGroups', 'secondaryMuscleGroups']);

        if ($this->option('skip-existing')) {
            $query->whereNull('muscle_group_image');
            $this->info('Skipping exercises that already have muscle group images.');
        }

        $exercises = $query->limit(10)->get();

        if ($exercises->isEmpty()) {
            $this->warn('No exercises found to process.');

            return Command::SUCCESS;
        }

        $this->info("Found {$exercises->count()} exercises to process.");
        $this->info('Processing exercises one by one...');
        $this->newLine();

        $total = $exercises->count();
        $current = 0;

        foreach ($exercises as $exercise) {
            $current++;
            $this->info("Processing exercise {$current}/{$total}: {$exercise->name} (ID: {$exercise->id})");

            $result = [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'status' => 'pending',
                'image_path' => null,
                'error' => null,
                'skipped_reason' => null,
            ];

            try {
                // Get muscle group names
                $primaryMuscles = $exercise->primaryMuscleGroups->pluck('name')->toArray();
                $secondaryMuscles = $exercise->secondaryMuscleGroups->pluck('name')->toArray();

                // Skip if no muscle groups
                if (empty($primaryMuscles) && empty($secondaryMuscles)) {
                    $result['status'] = 'skipped';
                    $result['skipped_reason'] = 'No muscle groups assigned';
                    $this->warn('  ⚠ Skipped - No muscle groups assigned');
                } else {
                    // Delete old image if exists
                    if ($exercise->muscle_group_image) {
                        Storage::delete($exercise->muscle_group_image);
                    }

                    // Fetch and store new image
                    $imagePath = $muscleGroupImageService->fetchAndStoreMuscleImage(
                        $primaryMuscles,
                        $secondaryMuscles
                    );

                    if ($imagePath === null) {
                        $result['status'] = 'failed';
                        $result['error'] = 'Failed to fetch muscle group image. API may be unavailable or muscle groups could not be normalized.';
                        $this->warn("  ✗ Failed - {$result['error']}");
                    } else {
                        // Update exercise with new image path
                        $exercise->update(['muscle_group_image' => $imagePath]);
                        $result['status'] = 'success';
                        $result['image_path'] = $imagePath;
                        $this->line('  ✓ Success - Image updated');
                    }
                }
            } catch (\Exception $e) {
                $result['status'] = 'failed';
                $result['error'] = $e->getMessage();
                Log::error('Error processing exercise muscle image', [
                    'exercise_id' => $exercise->id,
                    'exercise_name' => $exercise->name,
                    'error' => $e->getMessage(),
                ]);
                $this->warn("  ✗ Failed - {$result['error']}");
            }

            $this->results[] = $result;
            $this->newLine();
        }

        // Output summary
        $this->outputSummary();

        return Command::SUCCESS;
    }

    /**
     * Output summary statistics
     */
    private function outputSummary(): void
    {
        $summary = $this->getSummary();

        $this->newLine();
        $this->info('Summary:');
        $this->line("  Total: {$summary['total']}");
        $this->line("  Successful: {$summary['successful']}");
        $this->line("  Skipped: {$summary['skipped']}");
        $this->line("  Failed: {$summary['failed']}");
    }

    /**
     * Get summary statistics
     */
    private function getSummary(): array
    {
        $total = count($this->results);
        $successful = count(array_filter($this->results, fn ($r) => $r['status'] === 'success'));
        $skipped = count(array_filter($this->results, fn ($r) => $r['status'] === 'skipped'));
        $failed = count(array_filter($this->results, fn ($r) => $r['status'] === 'failed'));

        return [
            'total' => $total,
            'successful' => $successful,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }
}
