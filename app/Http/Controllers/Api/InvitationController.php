<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvitationResource;
use App\Models\UserInvitation;
use Illuminate\Http\JsonResponse;

class InvitationController extends Controller
{
    /**
     * Validate an invitation token and return partner info.
     */
    public function show(string $token): JsonResponse
    {
        $invitation = UserInvitation::with('partner.identity')
            ->where('token', $token)
            ->first();

        // Invitation not found
        if (! $invitation) {
            return response()->json([
                'message' => 'Invalid invitation token',
            ], 404);
        }

        // Invitation already accepted
        if ($invitation->isAccepted()) {
            return response()->json([
                'message' => 'This invitation has already been used',
            ], 422);
        }

        // Invitation expired
        if ($invitation->isExpired()) {
            return response()->json([
                'message' => 'This invitation has expired',
            ], 422);
        }

        return response()->json([
            'message' => 'Valid invitation',
            'data' => new InvitationResource($invitation),
        ]);
    }
}
