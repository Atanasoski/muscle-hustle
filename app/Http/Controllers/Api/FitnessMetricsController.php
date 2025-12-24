<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FitnessMetricsResource;
use App\Services\FitnessMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FitnessMetricsController extends Controller
{
    /**
     * Get fitness metrics for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $service = new FitnessMetricsService($user);
        $metrics = $service->getMetrics();

        $resource = new FitnessMetricsResource($metrics);

        return response()->json($resource->toArray(request()));
    }
}
