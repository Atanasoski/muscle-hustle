<?php

namespace App\Console\Commands;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassifyExercisesWithOpenAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exercises:classify-openai {--force : Actually update exercises in database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Classify exercises with muscle groups and categories using OpenAI';

    private string $apiKey;

    private string $model;

    private array $muscleGroups = [];

    private array $categories = [];

    private array $results = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->apiKey = config('nutrition.openai.api_key');
        $this->model = config('nutrition.openai.model', 'gpt-4');

        if (empty($this->apiKey)) {
            $this->error('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');

            return Command::FAILURE;
        }

        $this->info('Fetching exercises, muscle groups, and categories...');

        // Fetch all data
        // Only fetch exercises that are missing either primary or secondary muscle groups
        $exercises = Exercise::where(function ($query) {
            // Exercises with no primary muscle groups
            $query->whereDoesntHave('primaryMuscleGroups')
                // OR exercises with no secondary muscle groups
                ->orWhereDoesntHave('secondaryMuscleGroups');
        })->get();
        $this->muscleGroups = MuscleGroup::all()->toArray();
        $this->categories = Category::where('type', CategoryType::Workout)->get()->toArray();

        if ($exercises->isEmpty()) {
            $this->warn('No exercises found in database.');

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
                'ai_response' => null,
                'error' => null,
                'updates' => null,
            ];

            try {
                $aiResponse = $this->callOpenAI($exercise->name);

                if ($aiResponse === null) {
                    $result['status'] = 'failed';
                    $result['error'] = 'Failed to get response from OpenAI';
                } else {
                    $result['ai_response'] = $aiResponse;
                    $result['status'] = 'success';

                    // If --force flag is present, update the database
                    if ($this->option('force')) {
                        $updates = $this->updateExercise($exercise, $aiResponse);
                        $result['updates'] = $updates;
                    }
                }
            } catch (\Exception $e) {
                $result['status'] = 'failed';
                $result['error'] = $e->getMessage();
                Log::error('Error processing exercise', [
                    'exercise_id' => $exercise->id,
                    'exercise_name' => $exercise->name,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->results[] = $result;

            if ($result['status'] === 'success') {
                $this->line('  ✓ Success');
            } else {
                $this->warn("  ✗ Failed: {$result['error']}");
            }

            $this->newLine();
        }

        // Output results as JSON
        $this->outputResults();

        $summary = $this->getSummary();
        $this->newLine();
        $this->info('Summary:');
        $this->line("  Total: {$summary['total']}");
        $this->line("  Successful: {$summary['successful']}");
        $this->line("  Failed: {$summary['failed']}");

        if ($this->option('force')) {
            $this->info('  Database updates: Applied');
        } else {
            $this->warn('  Database updates: Not applied (use --force to apply)');
        }

        return Command::SUCCESS;
    }

    /**
     * Call OpenAI API to classify the exercise
     */
    private function callOpenAI(string $exerciseName): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $exerciseName,
                    ],
                ],
                'temperature' => 0.1, // Low temperature for more consistent results
            ]);

            if (! $response->successful()) {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'exercise_name' => $exerciseName,
                ]);

                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (empty($content)) {
                Log::error('Empty response from OpenAI', [
                    'exercise_name' => $exerciseName,
                ]);

                return null;
            }

            // Try to extract JSON from markdown code blocks if present
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*/', '', $content);
            $content = trim($content);

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response from OpenAI', [
                    'exercise_name' => $exerciseName,
                    'content' => $content,
                    'json_error' => json_last_error_msg(),
                ]);

                return null;
            }

            // Validate response structure
            if (! $this->validateResponse($data)) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('OpenAI API call failed', [
                'exercise_name' => $exerciseName,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get the system prompt for OpenAI
     */
    private function getSystemPrompt(): string
    {
        // Organize muscle groups by body region
        $muscleGroupsByRegion = [];
        foreach ($this->muscleGroups as $mg) {
            $region = $mg['body_region'] ?? 'other';
            if (! isset($muscleGroupsByRegion[$region])) {
                $muscleGroupsByRegion[$region] = [];
            }
            $muscleGroupsByRegion[$region][] = $mg['name'];
        }

        $muscleGroupsList = '';
        foreach ($muscleGroupsByRegion as $region => $names) {
            $regionLabel = ucfirst($region);
            $muscleGroupsList .= "\n{$regionLabel}: ".implode(', ', $names);
        }

        // Build categories list
        $categoriesList = '';
        foreach ($this->categories as $category) {
            $categoriesList .= "\n- {$category['name']} (slug: {$category['slug']})";
        }

        return <<<PROMPT
You are a fitness expert specializing in exercise classification. Analyze the exercise name provided and return ONLY a valid JSON object with this exact structure:

{
  "primary_muscle_groups": ["Chest", "Triceps"],
  "secondary_muscle_groups": ["Shoulders"],
  "category_slug": "strength-training"
}

Available Muscle Groups (you MUST only use these exact names):
{$muscleGroupsList}

Available Categories (you MUST only use these exact slugs):
{$categoriesList}

Rules:
1. Return ONLY valid JSON, no markdown, no explanations, no code blocks
2. Use ONLY the muscle group names listed above (exact match, case-insensitive)
3. Use ONLY the category slugs listed above (exact match)
4. Primary muscle groups are the main muscles targeted by the exercise
5. Secondary muscle groups are muscles that assist or stabilize during the exercise
6. If an exercise targets multiple primary muscles, list all of them
7. If no secondary muscles are involved, use an empty array: []
8. All muscle group names must match exactly from the available list
9. The category_slug must match exactly from the available list
10. If you cannot determine the classification, make your best educated guess based on the exercise name

Return the JSON object only, nothing else.
PROMPT;
    }

    /**
     * Validate the OpenAI response structure
     */
    private function validateResponse(array $data): bool
    {
        if (! isset($data['primary_muscle_groups']) || ! is_array($data['primary_muscle_groups'])) {
            Log::error('Invalid response: missing or invalid primary_muscle_groups');

            return false;
        }

        if (! isset($data['secondary_muscle_groups']) || ! is_array($data['secondary_muscle_groups'])) {
            Log::error('Invalid response: missing or invalid secondary_muscle_groups');

            return false;
        }

        if (! isset($data['category_slug']) || ! is_string($data['category_slug'])) {
            Log::error('Invalid response: missing or invalid category_slug');

            return false;
        }

        return true;
    }

    /**
     * Update exercise in database based on AI response
     */
    private function updateExercise(Exercise $exercise, array $aiResponse): array
    {
        $updates = [
            'category_id' => null,
            'muscle_groups_synced' => false,
        ];

        // Update category
        $categorySlug = $aiResponse['category_slug'];
        $category = collect($this->categories)->firstWhere('slug', $categorySlug);

        if ($category) {
            $exercise->category_id = $category['id'];
            $exercise->save();
            $updates['category_id'] = $category['id'];
        } else {
            Log::warning('Category not found', [
                'exercise_id' => $exercise->id,
                'category_slug' => $categorySlug,
            ]);
        }

        // Sync muscle groups
        $muscleGroupAttachments = [];

        // Process primary muscle groups
        foreach ($aiResponse['primary_muscle_groups'] as $muscleName) {
            $muscleGroup = $this->findMuscleGroupByName($muscleName);
            if ($muscleGroup) {
                $muscleGroupAttachments[$muscleGroup['id']] = ['is_primary' => true];
            } else {
                Log::warning('Muscle group not found', [
                    'exercise_id' => $exercise->id,
                    'muscle_name' => $muscleName,
                ]);
            }
        }

        // Process secondary muscle groups
        foreach ($aiResponse['secondary_muscle_groups'] as $muscleName) {
            $muscleGroup = $this->findMuscleGroupByName($muscleName);
            if ($muscleGroup) {
                // If already in array as primary, skip (can't be both)
                if (! isset($muscleGroupAttachments[$muscleGroup['id']])) {
                    $muscleGroupAttachments[$muscleGroup['id']] = ['is_primary' => false];
                }
            } else {
                Log::warning('Muscle group not found', [
                    'exercise_id' => $exercise->id,
                    'muscle_name' => $muscleName,
                ]);
            }
        }

        if (! empty($muscleGroupAttachments)) {
            $exercise->muscleGroups()->sync($muscleGroupAttachments);
            $updates['muscle_groups_synced'] = true;
            $updates['muscle_groups_count'] = count($muscleGroupAttachments);
        }

        return $updates;
    }

    /**
     * Find muscle group by name (case-insensitive)
     */
    private function findMuscleGroupByName(string $name): ?array
    {
        $nameLower = strtolower(trim($name));

        foreach ($this->muscleGroups as $mg) {
            if (strtolower(trim($mg['name'])) === $nameLower) {
                return $mg;
            }
        }

        return null;
    }

    /**
     * Output results as JSON
     */
    private function outputResults(): void
    {
        $summary = $this->getSummary();
        $output = [
            'exercises' => $this->results,
            'summary' => $summary,
        ];

        $this->newLine();
        $this->info('Results (JSON):');
        $this->line(json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get summary statistics
     */
    private function getSummary(): array
    {
        $total = count($this->results);
        $successful = count(array_filter($this->results, fn ($r) => $r['status'] === 'success'));
        $failed = $total - $successful;

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
        ];
    }
}
