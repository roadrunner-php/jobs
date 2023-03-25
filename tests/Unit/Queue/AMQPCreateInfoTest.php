<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\AMQP\ExchangeType;
use Spiral\RoadRunner\Jobs\Queue\AMQPCreateInfo;

final class AMQPCreateInfoTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo('test');

        $this->assertSame(AMQPCreateInfo::PREFETCH_DEFAULT_VALUE, $amqpCreateInfo->prefetch);
        $this->assertSame(AMQPCreateInfo::QUEUE_DEFAULT_VALUE, $amqpCreateInfo->queue);
        $this->assertSame(AMQPCreateInfo::EXCHANGE_DEFAULT_VALUE, $amqpCreateInfo->exchange);
        $this->assertSame(ExchangeType::Direct, $amqpCreateInfo->exchangeType);
        $this->assertSame(AMQPCreateInfo::ROUTING_KEY_DEFAULT_VALUE, $amqpCreateInfo->routingKey);
        $this->assertFalse($amqpCreateInfo->exclusive);
        $this->assertFalse($amqpCreateInfo->multipleAck);
        $this->assertFalse($amqpCreateInfo->requeueOnFail);
        $this->assertFalse($amqpCreateInfo->durable);
    }

    public function testCustomValues(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo(
            'test',
            5,
            200,
            'custom_queue',
            'custom_exchange',
            ExchangeType::Topics,
            'custom_routing_key',
            true,
            true,
            true,
            true
        );

        $this->assertSame(200, $amqpCreateInfo->prefetch);
        $this->assertSame('custom_queue', $amqpCreateInfo->queue);
        $this->assertSame('custom_exchange', $amqpCreateInfo->exchange);
        $this->assertSame(ExchangeType::Topics, $amqpCreateInfo->exchangeType);
        $this->assertSame('custom_routing_key', $amqpCreateInfo->routingKey);
        $this->assertTrue($amqpCreateInfo->exclusive);
        $this->assertTrue($amqpCreateInfo->multipleAck);
        $this->assertTrue($amqpCreateInfo->requeueOnFail);
        $this->assertTrue($amqpCreateInfo->durable);
    }

    public function testToArray()
    {
        $amqpCreateInfo = new AMQPCreateInfo(
            'test',
            5,
            200,
            'custom_queue',
            'custom_exchange',
            ExchangeType::Fanout,
            'custom_routing_key',
            true,
            true,
            true,
            true
        );

        $expectedArray = [
            'driver' => 'amqp',
            'name' => 'test',
            'priority' => 5,
            'prefetch' => 200,
            'queue' => 'custom_queue',
            'exchange' => 'custom_exchange',
            'exchange_type' => ExchangeType::Fanout->value,
            'routing_key' => 'custom_routing_key',
            'exclusive' => true,
            'multiple_ack' => true,
            'requeue_on_fail' => true,
            'durable' => true,
            'exchange_durable' => false,
            'consume_all' => false,
        ];

        $this->assertEquals($expectedArray, $amqpCreateInfo->toArray());
    }
}
