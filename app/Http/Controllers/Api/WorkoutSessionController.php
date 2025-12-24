<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutSessionCalendarRequest;
use App\Http\Resources\Api\WorkoutSessionCalendarResource;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutSessionController extends Controller
{
    /**
     * Display workout sessions for the calendar view within a date range.
     */
    public function calendar(WorkoutSessionCalendarRequest $request): JsonResponse
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

        $sessions = WorkoutSession::where('user_id', Auth::id())
            ->with('workoutTemplate')
            ->whereBetween('performed_at', [$startDate, $endDate])
            ->orderBy('performed_at')
            ->get();

        return response()->json([
            'data' => [
                'sessions' => WorkoutSessionCalendarResource::collection($sessions),
                'date_range' => [
                    'start' => $request->start_date,
                    'end' => $request->end_date,
                ],
            ],
        ]);
    }
}
