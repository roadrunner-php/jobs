<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\NatsCreateInfo;

#[Test]
final class NatsCreateInfoTest
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
            true,
            0,
        );

        Assert::same($natsCreateInfo->driver, Driver::NATS);
        Assert::same($natsCreateInfo->name, 'test_name');
        Assert::same($natsCreateInfo->priority, 3);
        Assert::same($natsCreateInfo->subject, 'test_subject');
        Assert::same($natsCreateInfo->stream, 'test_stream');
        Assert::same($natsCreateInfo->prefetch, 200);
        Assert::false($natsCreateInfo->deliverNew);
        Assert::same($natsCreateInfo->rateLimit, 300);
        Assert::true($natsCreateInfo->deleteStreamOnStop);
        Assert::true($natsCreateInfo->deleteAfterAck);
        Assert::same($natsCreateInfo->ackWait, 0);
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
            true,
            0,
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
            'ack_wait' => 0,
        ];

        Assert::same($natsCreateInfo->toArray(), $expectedArray);
    }
}
