<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MuscleGroupResource;
use App\Models\MuscleGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MuscleGroupController extends Controller
{
    /**
     * Display a listing of muscle groups.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MuscleGroup::query();

        // Filter by body region if provided
        if ($request->has('body_region')) {
            $query->where('body_region', $request->input('body_region'));
        }

        $muscleGroups = $query->orderBy('body_region')->orderBy('name')->get();

        return MuscleGroupResource::collection($muscleGroups);
    }

    /**
     * Display the specified muscle group with its exercises.
     */
    public function show(MuscleGroup $muscleGroup): MuscleGroupResource
    {
        $muscleGroup->load('exercises');

        return new MuscleGroupResource($muscleGroup);
    }
}
