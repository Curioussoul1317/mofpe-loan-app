<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_through_web(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'role' => User::ROLE_ADMINISTRATOR,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_web_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $response = $this
            ->from('/login')
            ->post('/login', [
                'email' => 'admin@example.test',
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_api_user_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'email' => 'officer@example.test',
            'password' => 'Password123!',
            'role' => User::ROLE_LOAN_OFFICER,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'officer@example.test',
            'password' => 'Password123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ]);

        $this->assertDatabaseCount(
            'personal_access_tokens',
            1
        );
    }

    public function test_api_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'officer@example.test',
            'password' => 'Password123!',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'officer@example.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }
}