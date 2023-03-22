<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\NatsCreateInfo;

final class NatsCreateInfoTest extends TestCase
{
    public function testCreateNatsCreateInfo(): void
    {
        $natsCreateInfo = new NatsCreateInfo(
            'test_name',
            'test_subject',
            'test_stream',
            3,
            200,
            false,
            300,
            true,
            true
        );

        $this->assertSame(Driver::NATS, $natsCreateInfo->driver);
        $this->assertSame('test_name', $natsCreateInfo->name);
        $this->assertSame(3, $natsCreateInfo->priority);
        $this->assertSame('test_subject', $natsCreateInfo->subject);
        $this->assertSame('test_stream', $natsCreateInfo->stream);
        $this->assertSame(200, $natsCreateInfo->prefetch);
        $this->assertFalse($natsCreateInfo->deliverNew);
        $this->assertSame(300, $natsCreateInfo->rateLimit);
        $this->assertTrue($natsCreateInfo->deleteStreamOnStop);
        $this->assertTrue($natsCreateInfo->deleteAfterAck);
    }

    public function testToArray(): void
    {
        $natsCreateInfo = new NatsCreateInfo(
            'test_name',
            'test_subject',
            'test_stream',
            3,
            200,
            false,
            300,
            true,
            true
        );

        $expectedArray = [
            'name' => 'test_name',
            'driver' => Driver::NATS->value,
            'priority' => 3,
            'prefetch' => 200,
            'subject' => 'test_subject',
            'deliver_new' => false,
            'rate_limit' => 300,
            'stream' => 'test_stream',
            'delete_stream_on_stop' => true,
            'delete_after_ack' => true,
        ];

        $this->assertSame($expectedArray, $natsCreateInfo->toArray());
    }
}
