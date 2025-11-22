<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Exercise;
use App\Services\PixabayService;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index()
    {
        $categories = Category::with(['exercises' => function ($query) {
            $query->orderBy('name');
        }])
            ->orderBy('display_order')
            ->get();

        return view('exercises.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'video_url' => 'nullable|url',
            'default_rest_sec' => 'nullable|integer|min:0',
        ]);

        Exercise::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'category_id' => $request->category_id,
            'video_url' => $request->video_url,
            'default_rest_sec' => $request->default_rest_sec ?? 90,
        ]);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise created successfully!');
    }

    public function update(Request $request, Exercise $exercise)
    {
        // Authorization: can only edit own exercises or global ones
        if ($exercise->user_id && $exercise->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'video_url' => 'nullable|url',
            'default_rest_sec' => 'nullable|integer|min:0',
        ]);

        $exercise->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'video_url' => $request->video_url,
            'default_rest_sec' => $request->default_rest_sec,
        ]);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise updated successfully!');
    }

    public function destroy(Exercise $exercise)
    {
        // Can only delete custom exercises (user's own)
        if (! $exercise->user_id || $exercise->user_id !== auth()->id()) {
            abort(403, 'Cannot delete global exercises');
        }

        $exercise->delete();

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise deleted successfully!');
    }

    public function searchPixabay(Request $request, PixabayService $pixabayService)
    {
        $query = $request->input('query', 'fitness workout');

        $results = $pixabayService->searchVideos($query);

        return response()->json($results);
    }

    public function downloadPixabayVideo(Request $request, Exercise $exercise, PixabayService $pixabayService)
    {
        $request->validate([
            'video_url' => 'required|url',
        ]);

        // Delete old video if exists
        if ($exercise->pixabay_video_path) {
            $pixabayService->deleteVideo($exercise->pixabay_video_path);
        }

        // Download and save new video
        $path = $pixabayService->downloadVideo($request->video_url, $exercise->id);

        if (! $path) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download video',
            ], 500);
        }

        // Update exercise
        $exercise->update(['pixabay_video_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Video downloaded successfully!',
            'path' => $path,
        ]);
    }

    public function deletePixabayVideo(Exercise $exercise, PixabayService $pixabayService)
    {
        if ($exercise->pixabay_video_path) {
            $pixabayService->deleteVideo($exercise->pixabay_video_path);
            $exercise->update(['pixabay_video_path' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video removed successfully!',
        ]);
    }
}
