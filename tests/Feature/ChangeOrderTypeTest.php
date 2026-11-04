<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChangeOrderTypeTest extends TestCase
{
    public function test_change_type_route_returns_not_found_for_unknown_order(): void { $this->patchJson('/api/v1/orders/1/type', [])->assertNotFound(); }
}
