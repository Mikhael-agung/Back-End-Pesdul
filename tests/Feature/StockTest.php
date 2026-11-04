<?php

namespace Tests\Feature;

use Tests\TestCase;

class StockTest extends TestCase
{
    public function test_stock_endpoint_is_available(): void { $this->getJson('/api/v1/internal/orders')->assertOk(); }
}
