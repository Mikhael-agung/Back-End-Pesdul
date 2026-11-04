<?php

namespace Tests\Feature;

use Tests\TestCase;

class ScanFlowTest extends TestCase
{
    public function test_scan_route_requires_order_number(): void { $this->postJson('/api/v1/internal/scan', [])->assertStatus(422); }
}
