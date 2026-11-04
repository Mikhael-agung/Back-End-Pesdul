<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentNotificationTest extends TestCase
{
    public function test_midtrans_webhook_returns_ok(): void { $this->postJson('/api/v1/webhooks/midtrans', [])->assertOk(); }
}
