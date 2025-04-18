<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_login_returns_error_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong password',
        ]);

        $response->assertStatus(422);
    }
}
