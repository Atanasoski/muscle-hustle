<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Muscle Group Image Service
 * Fetches skeleton images with highlighted muscle groups from RapidAPI
 */
class MuscleGroupImageService
{
    /**
     * Map of muscle group names from our database to the external API format
     *
     * Valid API muscle groups:
     * all, all_lower, all_upper, abductors, abs, adductors, back, back_lower, back_upper,
     * biceps, calfs, chest, core, core_lower, core_upper, forearms, gluteus, hamstring,
     * hands, latissimus, legs, neck, quadriceps, shoulders, shoulders_back, shoulders_front, triceps
     *
     * @var array<string, string>
     */
    private const MUSCLE_NAME_MAP = [
        // Chest
        'chest' => 'chest',

        // Back
        'lats' => 'latissimus',
        'latissimus' => 'latissimus',
        'upper back' => 'back_upper',
        'lower back' => 'back_lower',
        'back' => 'back',

        // Shoulders
        'front delts' => 'shoulders_front',
        'side delts' => 'shoulders',
        'rear delts' => 'shoulders_back',
        'shoulders' => 'shoulders',

        // Neck/Traps
        'traps' => 'neck',
        'neck' => 'neck',

        // Arms
        'biceps' => 'biceps',
        'triceps' => 'triceps',
        'forearms' => 'forearms',
        'hands' => 'hands',

        // Lower body
        'quadriceps' => 'quadriceps',
        'hamstrings' => 'hamstring',
        'hamstring' => 'hamstring',
        'glutes' => 'gluteus',
        'gluteus' => 'gluteus',
        'calves' => 'calfs',
        'calfs' => 'calfs',
        'legs' => 'legs',
        'abductors' => 'abductors',
        'adductors' => 'adductors',

        // Core
        'abs' => 'abs',
        'obliques' => 'core',
        'core' => 'core',
    ];

    /**
     * Default colors for primary and secondary muscles
     */
    private const DEFAULT_PRIMARY_COLOR = '8b5cf6';  // Purple

    private const DEFAULT_SECONDARY_COLOR = 'ec4899'; // Pink

    private string $apiKey;

    private string $apiHost;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rapidapi.key') ?? '';
        $this->apiHost = config('services.rapidapi.muscle_image.host') ?? '';
        $this->baseUrl = config('services.rapidapi.muscle_image.base_url') ?? '';
    }

    /**
     * Check if the service is configured with an API key
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Normalize muscle group name for the external API
     *
     * @param  string  $muscleName  Muscle name from our database
     * @return string|null Normalized muscle name for external API, or null if not mappable
     */
    public function normalizeMuscleGroupName(string $muscleName): ?string
    {
        $lowerName = strtolower(trim($muscleName));

        return self::MUSCLE_NAME_MAP[$lowerName] ?? null;
    }

    /**
     * Strip # from hex color and return just the hex value
     */
    private function stripHashFromColor(string $hexColor): string
    {
        return ltrim($hexColor, '#');
    }

    /**
     * Fetch muscle group image from RapidAPI
     *
     * @param  array<string>  $primaryMuscles  Array of primary muscle group names
     * @param  array<string>  $secondaryMuscles  Array of secondary muscle group names
     * @param  string  $primaryColor  Hex color for primary muscles (e.g., "#8b5cf6")
     * @param  string  $secondaryColor  Hex color for secondary muscles (e.g., "#ec4899")
     * @param  bool  $transparentBackground  Whether to use transparent background
     * @return string|null Binary content of the image or null if failed
     */
    public function fetchMuscleGroupImage(
        array $primaryMuscles = [],
        array $secondaryMuscles = [],
        string $primaryColor = self::DEFAULT_PRIMARY_COLOR,
        string $secondaryColor = self::DEFAULT_SECONDARY_COLOR,
        bool $transparentBackground = true
    ): ?string {
        if (! $this->isConfigured()) {
            Log::warning('[MuscleGroupImageService] RAPIDAPI_KEY not configured');

            return null;
        }

        // Normalize and filter valid muscle names
        $normalizedPrimary = array_filter(
            array_map(fn ($m) => $this->normalizeMuscleGroupName($m), $primaryMuscles)
        );

        $normalizedSecondary = array_filter(
            array_map(fn ($m) => $this->normalizeMuscleGroupName($m), $secondaryMuscles)
        );

        $allMuscles = array_merge($normalizedPrimary, $normalizedSecondary);

        if (empty($allMuscles)) {
            Log::warning('[MuscleGroupImageService] No valid muscle groups provided');

            return null;
        }

        // Build colors array - one color per valid muscle
        $primaryColorHex = $this->stripHashFromColor($primaryColor);
        $secondaryColorHex = $this->stripHashFromColor($secondaryColor);

        $colors = array_merge(
            array_fill(0, count($normalizedPrimary), $primaryColorHex),
            array_fill(0, count($normalizedSecondary), $secondaryColorHex)
        );

        // Build query params
        $params = [
            'muscleGroups' => implode(',', $allMuscles),
            'colors' => implode(',', $colors),
        ];

        if ($transparentBackground) {
            $params['transparentBackground'] = '1';
        }

        $url = $this->baseUrl.'?'.http_build_query($params);

        try {
            Log::info('[MuscleGroupImageService] Fetching muscle image', [
                'muscles' => $allMuscles,
                'colors' => $colors,
            ]);

            $response = Http::withHeaders([
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ])->timeout(30)->get($url);

            if (! $response->successful()) {
                throw new \RuntimeException("API returned {$response->status()}: {$response->body()}");
            }

            Log::info('[MuscleGroupImageService] Image fetched successfully');

            return $response->body();
        } catch (\Exception $e) {
            Log::error('[MuscleGroupImageService] Error fetching muscle image', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch and store muscle group image for an exercise
     *
     * @param  array<string>  $primaryMuscles  Array of primary muscle group names
     * @param  array<string>  $secondaryMuscles  Array of secondary muscle group names
     * @param  string|null  $filename  Custom filename (without extension), will be auto-generated if null
     * @param  string  $disk  Storage disk to use
     * @return string|null Storage path of the saved image or null if failed
     */
    public function fetchAndStoreMuscleImage(
        array $primaryMuscles = [],
        array $secondaryMuscles = [],
        ?string $filename = null,
    ): ?string {
        $imageContent = $this->fetchMuscleGroupImage($primaryMuscles, $secondaryMuscles);

        if ($imageContent === null) {
            return null;
        }

        // Generate filename if not provided
        if ($filename === null) {
            $muscleHash = md5(implode(',', array_merge($primaryMuscles, $secondaryMuscles)));
            $filename = 'muscle-image-'.$muscleHash;
        }

        // Sanitize filename
        $filename = Str::slug($filename);

        $path = "exercises/muscle-images/{$filename}.png";

        try {
            Storage::put($path, $imageContent);
            Log::info('[MuscleGroupImageService] Image stored successfully', ['path' => $path]);

            return $path;
        } catch (\Exception $e) {
            Log::error('[MuscleGroupImageService] Error storing muscle image', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get the public URL for a stored muscle image
     *
     * @param  string  $path  Storage path of the image
     * @param  string  $disk  Storage disk
     */
    public function getImageUrl(string $path): string
    {
        return Storage::url($path);
    }

    /**
     * Check if a muscle image already exists for the given muscles
     *
     * @param  array<string>  $primaryMuscles  Array of primary muscle group names
     * @param  array<string>  $secondaryMuscles  Array of secondary muscle group names
     * @param  string  $disk  Storage disk
     */
    public function imageExists(
        array $primaryMuscles,
        array $secondaryMuscles = [],
    ): bool {
        $muscleHash = md5(implode(',', array_merge($primaryMuscles, $secondaryMuscles)));
        $path = "exercises/muscle-images/muscle-image-{$muscleHash}.png";

        return Storage::exists($path);
    }

    /**
     * Get the path for a muscle image based on muscles
     *
     * @param  array<string>  $primaryMuscles  Array of primary muscle group names
     * @param  array<string>  $secondaryMuscles  Array of secondary muscle group names
     */
    public function getImagePath(array $primaryMuscles, array $secondaryMuscles = []): string
    {
        $muscleHash = md5(implode(',', array_merge($primaryMuscles, $secondaryMuscles)));

        return "exercises/muscle-images/muscle-image-{$muscleHash}.png";
    }
}
