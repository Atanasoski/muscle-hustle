<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'expires_at' => $this->expires_at->toIso8601String(),
            'partner' => [
                'id' => $this->partner->id,
                'name' => $this->partner->name,
                'slug' => $this->partner->slug,
                'visual_identity' => $this->when(
                    $this->partner->identity,
                    fn () => new PartnerVisualIdentityResource($this->partner->identity)
                ),
            ],
        ];
    }
}
