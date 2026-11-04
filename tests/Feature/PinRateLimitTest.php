<?php

namespace Tests\Feature;

use Tests\TestCase;

class PinRateLimitTest extends TestCase
{
    public function test_pin_login_requires_credentials(): void { $this->postJson('/api/v1/internal/pin/login', [])->assertStatus(422); }
}
