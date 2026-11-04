<?php

namespace Tests\Feature;

use App\Services\QueueEstimator;
use Tests\TestCase;

class QueueEstimatorTest extends TestCase
{
    public function test_queue_estimator_can_be_resolved(): void { $this->assertInstanceOf(QueueEstimator::class, app(QueueEstimator::class)); }
}
