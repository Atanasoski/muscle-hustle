<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => __('passwords.sent')]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_returns_422_for_invalid_email(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_returns_422_when_user_not_found(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message']);
    }

    public function test_reset_password_succeeds_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $this->postJson('/api/forgot-password', ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->assertNotNull($token);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => __('passwords.reset')]);

        $user->refresh();
        $this->assertNotSame($oldPasswordHash, $user->password);
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    public function test_reset_password_returns_422_for_invalid_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message']);
    }

    public function test_reset_password_returns_422_for_validation_errors(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'some-token',
            'email' => 'user@example.com',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }
}
