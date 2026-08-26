<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/users', [
            'name' => 'New Officer',
            'email' => 'new@example.test',
            'password' => 'Password123!',
            'role' => User::ROLE_LOAN_OFFICER,
        ])->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.test',
            'role' => User::ROLE_LOAN_OFFICER,
        ]);
    }

    public function test_officer_cannot_manage_users(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_LOAN_OFFICER,
        ]);

        Sanctum::actingAs($officer);

        $this->getJson('/api/users')
            ->assertForbidden();
    }
}