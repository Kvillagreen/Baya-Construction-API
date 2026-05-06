<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_authenticated_user_payload(): void
    {
        $this->seed();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@bayaconstruction.com',
            'password' => 'BayaSecure!2026',
            'honeypot' => '',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.user.email', 'admin@bayaconstruction.com');
    }
}
