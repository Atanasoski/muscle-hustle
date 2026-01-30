<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_invitation(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'newuser@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invitation_token' => $invitation->token,
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'partner'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'partner_id' => $partner->id,
        ]);

        // Verify invitation was marked as accepted
        $invitation->refresh();
        $this->assertNotNull($invitation->accepted_at);
    }

    public function test_registration_requires_invitation_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['invitation_token']);
    }

    public function test_registration_fails_with_invalid_invitation_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invitation_token' => 'invalid-token',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['invitation_token']);
    }

    public function test_registration_fails_with_expired_invitation(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'newuser@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->subDays(1), // Expired
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invitation_token' => $invitation->token,
        ]);

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has expired',
            ]);
    }

    public function test_registration_fails_with_already_used_invitation(): void
    {
        $partner = Partner::factory()->create();
        $inviter = User::factory()->create(['partner_id' => $partner->id]);

        $invitation = UserInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $inviter->id,
            'email' => 'newuser@example.com',
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(), // Already accepted
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invitation_token' => $invitation->token,
        ]);

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has already been used',
            ]);
    }

    public function test_registration_fails_when_email_does_not_match_invitation(): void
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

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'different@example.com', // Different email
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invitation_token' => $invitation->token,
        ]);

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'Email does not match the invitation',
            ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        // Verify token was deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
