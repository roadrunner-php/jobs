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

        $this->assertSame('test', $amqpCreateInfo->name);
        $this->assertSame(AMQPCreateInfo::PRIORITY_DEFAULT_VALUE, $amqpCreateInfo->priority);
        $this->assertSame(AMQPCreateInfo::PREFETCH_DEFAULT_VALUE, $amqpCreateInfo->prefetch);
        $this->assertSame(AMQPCreateInfo::QUEUE_DEFAULT_VALUE, $amqpCreateInfo->queue);
        $this->assertSame(AMQPCreateInfo::EXCHANGE_DEFAULT_VALUE, $amqpCreateInfo->exchange);
        $this->assertSame(ExchangeType::Direct, $amqpCreateInfo->exchangeType);
        $this->assertSame(AMQPCreateInfo::ROUTING_KEY_DEFAULT_VALUE, $amqpCreateInfo->routingKey);
        $this->assertFalse($amqpCreateInfo->exclusive);
        $this->assertFalse($amqpCreateInfo->multipleAck);
        $this->assertFalse($amqpCreateInfo->requeueOnFail);
        $this->assertFalse($amqpCreateInfo->durable);
        $this->assertSame(AMQPCreateInfo::EXCHANGE_DURABLE_DEFAULT_VALUE, $amqpCreateInfo->exchangeDurable);
        $this->assertSame(AMQPCreateInfo::QUEUE_HEADERS_DEFAULT_VALUE, $amqpCreateInfo->queueHeaders);
        $this->assertSame(AMQPCreateInfo::DELETE_QUEUE_ON_STOP_DEFAULT_VALUE, $amqpCreateInfo->deleteQueueOnStop);
        $this->assertSame(AMQPCreateInfo::REDIAL_TIMEOUT_DEFAULT_VALUE, $amqpCreateInfo->redialTimeout);
        $this->assertSame(AMQPCreateInfo::EXCHANGE_AUTO_DELETE_DEFAULT_VALUE, $amqpCreateInfo->exchangeAutoDelete);
        $this->assertSame(AMQPCreateInfo::QUEUE_AUTO_DELETE_DEFAULT_VALUE, $amqpCreateInfo->queueAutoDelete);
        $this->assertSame(AMQPCreateInfo::CONSUMER_ID_DEFAULT_VALUE, $amqpCreateInfo->consumerId);
    }

    public function testCustomValues(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo(
            name: 'test',
            priority: 5,
            prefetch: 200,
            queue: 'custom_queue',
            exchange: 'custom_exchange',
            exchangeType: ExchangeType::Topics,
            routingKey: 'custom_routing_key',
            exclusive: true,
            multipleAck: true,
            requeueOnFail: true,
            durable: true,
            exchangeDurable: true,
            queueHeaders: [
                'x-queue-type' => 'quorum',
            ],
            deleteQueueOnStop: true,
            redialTimeout: 10,
            exchangeAutoDelete: true,
            queueAutoDelete: true,
            consumerId: 'custom_consumer_id'
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
        $this->assertTrue($amqpCreateInfo->exchangeDurable);
        $this->assertSame(['x-queue-type' => 'quorum'], $amqpCreateInfo->queueHeaders);
        $this->assertTrue($amqpCreateInfo->deleteQueueOnStop);
        $this->assertSame(10, $amqpCreateInfo->redialTimeout);
        $this->assertTrue($amqpCreateInfo->exchangeAutoDelete);
        $this->assertTrue($amqpCreateInfo->queueAutoDelete);
        $this->assertSame('custom_consumer_id', $amqpCreateInfo->consumerId);
    }

    public function testToArray(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo(
            name: 'test',
            priority: 5,
            prefetch: 200,
            queue: 'custom_queue',
            exchange: 'custom_exchange',
            exchangeType: ExchangeType::Fanout,
            routingKey: 'custom_routing_key',
            exclusive: true,
            multipleAck: true,
            requeueOnFail: true,
            durable: true,
            exchangeDurable: true,
            queueHeaders: [
                'x-queue-type' => 'quorum',
            ],
            deleteQueueOnStop: true,
            redialTimeout: 10,
            exchangeAutoDelete: true,
            queueAutoDelete: true,
            consumerId: 'custom_consumer_id'
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
            'exchange_durable' => true,
            'queue_headers' => [
                'x-queue-type' => 'quorum',
            ],
            'exchange_auto_delete' => true,
            'delete_queue_on_stop' => true,
            'redial_timeout' => 10,
            'queue_auto_delete' => true,
            'consumer_id' => 'custom_consumer_id'
        ];

        $this->assertEquals($expectedArray, $amqpCreateInfo->toArray());
    }

    public function testToArrayWithEmptyQueueHeaders(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo(name: 'foo');

        $this->assertArrayNotHasKey('queue_headers', $amqpCreateInfo->toArray());
    }
}
