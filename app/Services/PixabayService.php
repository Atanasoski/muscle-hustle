<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PixabayService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://pixabay.com/api/videos';

    public function __construct()
    {
        $this->apiKey = config('services.pixabay.key');
    }

    public function searchVideos(string $query, int $perPage = 15): array
    {
        $response = Http::get($this->baseUrl, [
            'key' => $this->apiKey,
            'q' => $query.' fitness workout exercise', // Add fitness context
            'per_page' => min($perPage, 40), // Get more results to filter from
            'safesearch' => 'true',
            'order' => 'popular', // Sort by popularity
            'video_type' => 'all',
        ]);

        if (! $response->successful()) {
            return ['videos' => []];
        }

        $data = $response->json();

        // Filter for short videos (8-30 seconds) and return simplified data
        $videos = collect($data['hits'] ?? [])
            ->filter(fn ($video) => $video['duration'] >= 8 && $video['duration'] <= 30)
            ->map(function ($video) {
                // Prefer large quality, fallback to medium
                $videoFile = $video['videos']['large'] ?? $video['videos']['medium'] ?? $video['videos']['small'] ?? null;

                if (! $videoFile) {
                    return null;
                }

                return [
                    'id' => $video['id'],
                    'duration' => $video['duration'],
                    'image' => $videoFile['thumbnail'] ?? '',
                    'video_url' => $videoFile['url'],
                    'width' => $videoFile['width'] ?? 0,
                    'height' => $videoFile['height'] ?? 0,
                ];
            })
            ->filter()
            ->values()
            ->take(12) // Show more results
            ->toArray();

        return ['videos' => $videos];
    }

    public function downloadVideo(string $videoUrl, int $exerciseId): ?string
    {
        try {
            // Download video content
            $videoContent = Http::timeout(120)->get($videoUrl)->body();

            // Create directory structure
            $directory = "exercises/{$exerciseId}";
            Storage::disk('public')->makeDirectory($directory);

            // Generate unique filename
            $filename = "{$directory}/".uniqid().'.mp4';

            // Save to storage/app/public/exercises/{id}/
            Storage::disk('public')->put($filename, $videoContent);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('Pixabay video download failed: '.$e->getMessage());

            return null;
        }
    }

    public function deleteVideo(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }
}
