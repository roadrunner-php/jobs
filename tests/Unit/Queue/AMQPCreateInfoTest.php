<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\AMQP\ExchangeType;
use Spiral\RoadRunner\Jobs\Queue\AMQPCreateInfo;
use Testo\Assert;
use Testo\Test;

#[Test]
final class AMQPCreateInfoTest
{
    public function testDefaultValues(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo('test');

        Assert::same($amqpCreateInfo->name, 'test');
        Assert::same($amqpCreateInfo->priority, AMQPCreateInfo::PRIORITY_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->prefetch, AMQPCreateInfo::PREFETCH_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->queue, AMQPCreateInfo::QUEUE_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->exchange, AMQPCreateInfo::EXCHANGE_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->exchangeType, ExchangeType::Direct);
        Assert::same($amqpCreateInfo->routingKey, AMQPCreateInfo::ROUTING_KEY_DEFAULT_VALUE);
        Assert::false($amqpCreateInfo->exclusive);
        Assert::false($amqpCreateInfo->multipleAck);
        Assert::false($amqpCreateInfo->requeueOnFail);
        Assert::false($amqpCreateInfo->durable);
        Assert::same($amqpCreateInfo->exchangeDurable, AMQPCreateInfo::EXCHANGE_DURABLE_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->queueHeaders, AMQPCreateInfo::QUEUE_HEADERS_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->deleteQueueOnStop, AMQPCreateInfo::DELETE_QUEUE_ON_STOP_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->redialTimeout, AMQPCreateInfo::REDIAL_TIMEOUT_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->exchangeAutoDelete, AMQPCreateInfo::EXCHANGE_AUTO_DELETE_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->queueAutoDelete, AMQPCreateInfo::QUEUE_AUTO_DELETE_DEFAULT_VALUE);
        Assert::same($amqpCreateInfo->consumerId, AMQPCreateInfo::CONSUMER_ID_DEFAULT_VALUE);
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
            consumerId: 'custom_consumer_id',
        );

        Assert::same($amqpCreateInfo->prefetch, 200);
        Assert::same($amqpCreateInfo->queue, 'custom_queue');
        Assert::same($amqpCreateInfo->exchange, 'custom_exchange');
        Assert::same($amqpCreateInfo->exchangeType, ExchangeType::Topics);
        Assert::same($amqpCreateInfo->routingKey, 'custom_routing_key');
        Assert::true($amqpCreateInfo->exclusive);
        Assert::true($amqpCreateInfo->multipleAck);
        Assert::true($amqpCreateInfo->requeueOnFail);
        Assert::true($amqpCreateInfo->durable);
        Assert::true($amqpCreateInfo->exchangeDurable);
        Assert::same($amqpCreateInfo->queueHeaders, ['x-queue-type' => 'quorum']);
        Assert::true($amqpCreateInfo->deleteQueueOnStop);
        Assert::same($amqpCreateInfo->redialTimeout, 10);
        Assert::true($amqpCreateInfo->exchangeAutoDelete);
        Assert::true($amqpCreateInfo->queueAutoDelete);
        Assert::same($amqpCreateInfo->consumerId, 'custom_consumer_id');
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
            consumerId: 'custom_consumer_id',
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
            'consumer_id' => 'custom_consumer_id',
        ];

        Assert::equals($amqpCreateInfo->toArray(), $expectedArray);
    }

    public function testToArrayWithEmptyQueueHeaders(): void
    {
        $amqpCreateInfo = new AMQPCreateInfo(name: 'foo');

        Assert::array($amqpCreateInfo->toArray())->doesNotHaveKeys('queue_headers');
    }
}
