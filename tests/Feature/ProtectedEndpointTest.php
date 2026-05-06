<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProtectedEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_endpoint_requires_authentication(): void
    {
        $this->seed();

        $this->getJson('/api/v1/users')->assertUnauthorized();
    }
}
