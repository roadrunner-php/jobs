<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\AMQP\ExchangeType;

/**
 * The DTO to create the AMQP driver.
 *
 * @see ExchangeType
 */
final class AMQPCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const PREFETCH_DEFAULT_VALUE = 100;

    /**
     * @var non-empty-string
     */
    public const QUEUE_DEFAULT_VALUE = 'default';

    /**
     * @var non-empty-string
     */
    public const EXCHANGE_DEFAULT_VALUE = 'amqp.default';

    /**
     * @var string
     */
    public const ROUTING_KEY_DEFAULT_VALUE = '';

    /**
     * @var bool
     */
    public const EXCLUSIVE_DEFAULT_VALUE = false;

    /**
     * @var bool
     */
    public const MULTIPLE_ACK_DEFAULT_VALUE = false;

    /**
     * @var bool
     */
    public const REQUEUE_ON_FAIL_DEFAULT_VALUE = false;

    /**
     * @var bool
     */
    public const DURABLE_DEFAULT_VALUE = false;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     * @param non-empty-string $queue
     * @param non-empty-string $exchange
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
        public readonly bool $durable = self::DURABLE_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::AMQP, $name, $priority);

        \assert($this->prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        \assert($this->queue !== '', 'Precondition [queue !== ""] failed');
        \assert($this->exchange !== '', 'Precondition [exchange !== ""] failed');
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch'        => $this->prefetch,
            'queue'           => $this->queue,
            'exchange'        => $this->exchange,
            'exchange_type'   => $this->exchangeType->value,
            'routing_key'     => $this->routingKey,
            'exclusive'       => $this->exclusive,
            'multiple_ack'    => $this->multipleAck,
            'requeue_on_fail' => $this->requeueOnFail,
            'durable'         => $this->durable,
        ]);
    }
}
