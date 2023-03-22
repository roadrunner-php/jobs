<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\AMQP\ExchangeType;

/**
 * The DTO to create the AMQP driver.
 *
 * @psalm-import-type DriverType from Driver
 * @psalm-import-type ExchangeTypeEnum from ExchangeType
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
     * @var ExchangeTypeEnum
     */
    public const EXCHANGE_TYPE_DEFAULT_VALUE = ExchangeType::TYPE_DIRECT;

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
     * @param ExchangeTypeEnum $exchangeType
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE,
        public readonly string $queue = self::QUEUE_DEFAULT_VALUE,
        public readonly string $exchange = self::EXCHANGE_DEFAULT_VALUE,
        public readonly string $exchangeType = self::EXCHANGE_TYPE_DEFAULT_VALUE,
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
        \assert($this->exchangeType !== '', 'Precondition [exchangeType !== ""] failed');
    }

    /**
     * @return array{
     *     name: non-empty-string,
     *     driver: DriverType,
     *     priority: positive-int,
     *     prefetch: positive-int,
     *     queue: non-empty-string,
     *     exchange: non-empty-string,
     *     exchange_type: ExchangeTypeEnum,
     *     routing_key: string,
     *     exclusive: bool,
     *     multiple_ack: bool,
     *     requeue_on_fail: bool,
     *     durable: bool
     * }
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch'        => $this->prefetch,
            'queue'           => $this->queue,
            'exchange'        => $this->exchange,
            'exchange_type'   => $this->exchangeType,
            'routing_key'     => $this->routingKey,
            'exclusive'       => $this->exclusive,
            'multiple_ack'    => $this->multipleAck,
            'requeue_on_fail' => $this->requeueOnFail,
            'durable'         => $this->durable,
        ]);
    }
}
