<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutTest extends TestCase
{
    public function test_menus_endpoint_is_available(): void { $this->getJson('/api/v1/menus')->assertOk(); }
}
