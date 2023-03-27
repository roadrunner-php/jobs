<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\AMQP\ExchangeType;

final class AMQPCreateInfo extends CreateInfo
{
    public const PREFETCH_DEFAULT_VALUE = 100;
    public const QUEUE_DEFAULT_VALUE = 'default';
    public const EXCHANGE_DEFAULT_VALUE = 'amqp.default';
    public const EXCHANGE_DURABLE_DEFAULT_VALUE = false;
    public const ROUTING_KEY_DEFAULT_VALUE = '';
    public const EXCLUSIVE_DEFAULT_VALUE = false;
    public const MULTIPLE_ACK_DEFAULT_VALUE = false;
    public const REQUEUE_ON_FAIL_DEFAULT_VALUE = false;
    public const DURABLE_DEFAULT_VALUE = false;
    public const CONSUME_ALL_DEFAULT_VALUE = false;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     * @param string $queue Queue name. Required for consumer.
     * @param non-empty-string $exchange
     * @param string $routingKey Routing key. Required for publisher.
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE,
        public readonly string $queue = self::QUEUE_DEFAULT_VALUE,
        public readonly string $exchange = self::EXCHANGE_DEFAULT_VALUE,
        public readonly ExchangeType $exchangeType = ExchangeType::Direct,
        public readonly string $routingKey = self::ROUTING_KEY_DEFAULT_VALUE,
        public readonly bool $exclusive = self::EXCLUSIVE_DEFAULT_VALUE,
        public readonly bool $multipleAck = self::MULTIPLE_ACK_DEFAULT_VALUE,
        public readonly bool $requeueOnFail = self::REQUEUE_ON_FAIL_DEFAULT_VALUE,
        public readonly bool $durable = self::DURABLE_DEFAULT_VALUE,
        public readonly bool $exchangeDurable = self::EXCHANGE_DURABLE_DEFAULT_VALUE,
        public readonly bool $consumeAll = self::CONSUME_ALL_DEFAULT_VALUE,
    ) {
        parent::__construct(Driver::AMQP, $name, $priority);

        \assert($this->prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        \assert($this->exchange !== '', 'Precondition [exchange !== ""] failed');
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch'        => $this->prefetch,
            'queue'           => $this->queue,
            'exchange'        => $this->exchange,
            'exchange_durable' => $this->exchangeDurable,
            'exchange_type'   => $this->exchangeType->value,
            'routing_key'     => $this->routingKey,
            'exclusive'       => $this->exclusive,
            'multiple_ack'    => $this->multipleAck,
            'requeue_on_fail' => $this->requeueOnFail,
            'durable'         => $this->durable,
            'consume_all'      => $this->consumeAll,
        ]);
    }
}
