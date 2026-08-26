<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_works(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_api_login_returns_token(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'user',
            ]);
    }

    public function test_api_requires_login(): void
    {
        $this->getJson('/api/loans')
            ->assertUnauthorized();
    }
}