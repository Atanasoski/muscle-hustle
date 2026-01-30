<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_invitation_token_returns_partner_info(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->getJson("/api/invitations/{$invitation->token}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Valid invitation',
                'data' => [
                    'token' => $invitation->token,
                    'email' => 'invited@example.com',
                    'partner' => [
                        'id' => $partner->id,
                        'name' => $partner->name,
                        'slug' => $partner->slug,
                    ],
                ],
            ]);
    }

    public function test_invalid_invitation_token_returns_404(): void
    {
        $response = $this->getJson('/api/invitations/invalid-token-123');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Invalid invitation token',
            ]);
    }

    public function test_expired_invitation_returns_422(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->subDays(1), // Expired yesterday
        ]);

        $response = $this->getJson("/api/invitations/{$invitation->token}");

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has expired',
            ]);
    }

    public function test_accepted_invitation_returns_422(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(), // Already accepted
        ]);

        $response = $this->getJson("/api/invitations/{$invitation->token}");

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has already been used',
            ]);
    }
}
