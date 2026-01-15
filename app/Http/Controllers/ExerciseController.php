<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\BulkLinkExerciseRequest;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Http\Requests\UpdatePartnerExerciseRequest;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->hasRole('admin')) {
            abort(403, 'Only system administrators can access this page.');
        }

        $categories = Category::where('type', CategoryType::Workout)
            ->with(['exercises' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('display_order')
            ->get();

        return view('exercises.admin.index', compact('categories'));
    }

    public function partnerIndex()
    {
        $user = auth()->user();

        if (! $user->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can access this page.');
        }

        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to view exercises.');
        }

        $categories = Category::where('type', CategoryType::Workout)
            ->with(['exercises' => function ($query) use ($partner) {
                $query->with(['partners' => function ($q) use ($partner) {
                        $q->where('partners.id', $partner->id)
                            ->withPivot(['description', 'image_url', 'video_url']);
                    }])
                    ->orderBy('name');
            }])
            ->orderBy('display_order')
            ->get();

        // Add link status and prepare data for each exercise
        $linkedExerciseIds = $partner->exercises()->get()->modelKeys();
        foreach ($categories as $category) {
            foreach ($category->exercises as $exercise) {
                // Set link status
                $exercise->is_linked = in_array($exercise->id, $linkedExerciseIds);

                // Get pivot data if available
                $pivot = null;
                if ($exercise->relationLoaded('partners') && $exercise->partners->isNotEmpty()) {
                    $pivot = $exercise->partners->first()->pivot;
                }

                // Compute effective values (pivot override or exercise default)
                $exercise->effective_description = $pivot?->description ?? $exercise->description;
                $exercise->effective_image_url = $pivot?->image_url ?? $exercise->image_url;
                $exercise->effective_video_url = $pivot?->video_url ?? $exercise->video_url;

                // Store pivot data for editing forms
                $exercise->pivot_data = $pivot;
            }
        }

        return view('exercises.partner.index', compact('categories', 'partner'));
    }

    public function store(StoreExerciseRequest $request)
    {
        Exercise::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'default_rest_sec' => $request->default_rest_sec ?? 90,
        ]);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise created successfully!');
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $exercise->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'default_rest_sec' => $request->default_rest_sec,
        ]);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise updated successfully!');
    }

    /**
     * Update partner exercise customization (pivot table).
     */
    public function updatePartnerExercises(UpdatePartnerExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $user = auth()->user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to customize exercises.');
        }

        $existingPivot = $partner->exercises()->find($exercise->id);
        $pivot = $existingPivot?->pivot;

        $pivotData = [
            'description' => $pivot?->description,
            'image_url' => $pivot?->image_url,
            'video_url' => $pivot?->video_url,
        ];

        // Update description (allow setting to null/empty to use default)
        if ($request->has('description')) {
            $pivotData['description'] = $request->description ?: null;
        }

        if ($request->hasFile('image')) {
            if ($pivot?->image_url) {
                Storage::disk('public')->delete(str_replace('storage/', '', $pivot->image_url));
            }
            $pivotData['image_url'] = 'storage/'.$request->file('image')->store('exercises/images', 'public');
        }

        if ($request->hasFile('video')) {
            if ($pivot?->video_url) {
                Storage::disk('public')->delete(str_replace('storage/', '', $pivot->video_url));
            }
            $pivotData['video_url'] = 'storage/'.$request->file('video')->store('exercises/videos', 'public');
        }

        if ($existingPivot) {
            $partner->exercises()->updateExistingPivot($exercise->id, $pivotData);
        } else {
            $partner->exercises()->attach($exercise->id, $pivotData);
        }

        return redirect()->route('partner.exercises.show', $exercise)
            ->with('success', 'Exercise customization updated successfully!');
    }

    public function destroy(Exercise $exercise)
    {
        $user = auth()->user();

        if (! $user->hasRole('admin')) {
            abort(403, 'Only system administrators can delete exercises.');
        }

        $exercise->delete();

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise deleted successfully!');
    }

    /**
     * Link an exercise to the user's partner.
     */
    public function linkExercise(Exercise $exercise): RedirectResponse
    {
        $user = auth()->user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to link exercises.');
        }

        // Link exercise to partner with null pivot values (will use exercise defaults)
        $partner->exercises()->syncWithoutDetaching([
            $exercise->id => [
                'description' => null,
                'image_url' => null,
                'video_url' => null,
            ],
        ]);

        return redirect()->back()
            ->with('success', 'Exercise linked successfully!');
    }

    /**
     * Unlink an exercise from the user's partner.
     */
    public function unlinkExercise(Exercise $exercise): RedirectResponse
    {
        $user = auth()->user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to unlink exercises.');
        }

        // Delete custom files if they exist
        $pivotData = $partner->exercises()->find($exercise->id);
        if ($pivotData && $pivotData->pivot) {
            if ($pivotData->pivot->image_url) {
                $imagePath = str_replace('storage/', '', $pivotData->pivot->image_url);
                Storage::disk('public')->delete($imagePath);
            }
            if ($pivotData->pivot->video_url) {
                $videoPath = str_replace('storage/', '', $pivotData->pivot->video_url);
                Storage::disk('public')->delete($videoPath);
            }
        }

        // Unlink exercise from partner
        $partner->exercises()->detach($exercise->id);

        return redirect()->back()
            ->with('success', 'Exercise unlinked successfully!');
    }

    /**
     * Show exercise details for partner.
     */
    public function show(Exercise $exercise)
    {
        $user = auth()->user();

        if (! $user->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can access this page.');
        }

        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to view exercises.');
        }

        // Load exercise with partner pivot data
        $exercise->load(['partners' => function ($q) use ($partner) {
            $q->where('partners.id', $partner->id)
                ->withPivot(['description', 'image_url', 'video_url']);
        }, 'category']);

        // Get pivot data if available
        $pivot = null;
        if ($exercise->relationLoaded('partners') && $exercise->partners->isNotEmpty()) {
            $pivot = $exercise->partners->first()->pivot;
        }

        // Compute effective values (pivot override or exercise default)
        $effectiveDescription = $pivot?->description ?? $exercise->description;
        $effectiveImageUrl = $pivot?->image_url ?? $exercise->image_url;
        $effectiveVideoUrl = $pivot?->video_url ?? $exercise->video_url;

        // Check if exercise is linked
        $isLinked = $partner->exercises()->where('workout_exercises.id', $exercise->id)->exists();

        return view('exercises.partner.show', compact('exercise', 'partner', 'pivot', 'effectiveDescription', 'effectiveImageUrl', 'effectiveVideoUrl', 'isLinked'));
    }

    /**
     * Show edit form for partner exercise customization.
     */
    public function edit(Exercise $exercise)
    {
        $user = auth()->user();

        if (! $user->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can access this page.');
        }

        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You must be associated with a partner to customize exercises.');
        }

        // Load exercise with partner pivot data
        $exercise->load(['partners' => function ($q) use ($partner) {
            $q->where('partners.id', $partner->id)
                ->withPivot(['description', 'image_url', 'video_url']);
        }, 'category']);

        // Get pivot data if available
        $pivot = null;
        if ($exercise->relationLoaded('partners') && $exercise->partners->isNotEmpty()) {
            $pivot = $exercise->partners->first()->pivot;
        }

        // Prepare form data (pivot values or defaults)
        $formDescription = $pivot?->description ?? '';
        $formImageUrl = $pivot?->image_url ?? null;
        $formVideoUrl = $pivot?->video_url ?? null;

        return view('exercises.partner.edit', compact('exercise', 'partner', 'pivot', 'formDescription', 'formImageUrl', 'formVideoUrl'));
    }

    /**
     * Bulk link exercises to partner.
     */
    public function bulkLink(BulkLinkExerciseRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $partner = $user->partner;

        $exerciseIds = $request->validated()['exercise_ids'];

        // Get currently linked exercise IDs
        $alreadyLinkedIds = $partner->exercises()->whereIn('workout_exercises.id', $exerciseIds)->pluck('workout_exercises.id')->toArray();

        // Prepare pivot data for all exercises (null values = use exercise defaults)
        $pivotData = [];
        foreach ($exerciseIds as $exerciseId) {
            $pivotData[$exerciseId] = [
                'description' => null,
                'image_url' => null,
                'video_url' => null,
            ];
        }

        // Link all exercises (syncWithoutDetaching won't duplicate already linked ones)
        $partner->exercises()->syncWithoutDetaching($pivotData);

        // Count newly linked exercises
        $newlyLinkedCount = count($exerciseIds) - count($alreadyLinkedIds);

        if ($newlyLinkedCount > 0) {
            return redirect()->route('partner.exercises.index')
                ->with('success', "{$newlyLinkedCount} exercise(s) linked successfully!");
        }

        return redirect()->route('partner.exercises.index')
            ->with('info', 'All selected exercises were already linked.');
    }
}
