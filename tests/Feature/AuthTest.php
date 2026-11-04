<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_registration_endpoint_is_available(): void { $this->postJson('/api/v1/auth/register', ['name' => 'A', 'email' => 'a@example.com', 'password' => 'password', 'password_confirmation' => 'password'])->assertCreated(); }
}
