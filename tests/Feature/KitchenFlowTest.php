<?php

namespace Tests\Feature;

use Tests\TestCase;

class KitchenFlowTest extends TestCase
{
    public function test_kitchen_endpoint_is_available(): void { $this->getJson('/api/v1/internal/kitchen')->assertOk(); }
}
