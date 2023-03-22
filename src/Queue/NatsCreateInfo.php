<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the NATS driver.
 *
 * @psalm-import-type DriverType from Driver
 */
final class NatsCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const PREFETCH_DEFAULT_VALUE = 100;

    /**
     * @var bool
     */
    public const DELIVER_NEW_DEFAULT_VALUE = true;

    /**
     * @var positive-int
     */
    public const RATE_LIMIT_DEFAULT_VALUE = 100;

    /**
     * @var bool
     */
    public const DELETE_STREAM_ON_STOP_DEFAULT_VALUE = false;

    /**
     * @var bool
     */
    public const DELETE_AFTER_ACK_DEFAULT_VALUE = false;

    /**
     * @var positive-int
     */
    public const PRIORITY_DEFAULT_VALUE = 2;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $subject
     * @param non-empty-string $stream
     * @param positive-int $priority
     * @param positive-int $prefetch
     * @param positive-int $rateLimit
     */
    public function __construct(
        string $name,
        public readonly string $subject,
        public readonly string $stream,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE,
        public readonly bool $deliverNew = self::DELIVER_NEW_DEFAULT_VALUE,
        public readonly int $rateLimit = self::RATE_LIMIT_DEFAULT_VALUE,
        public readonly bool $deleteStreamOnStop = self::DELETE_STREAM_ON_STOP_DEFAULT_VALUE,
        public readonly bool $deleteAfterAck = self::DELETE_AFTER_ACK_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::NATS, $name, $priority);

        \assert($prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        \assert($rateLimit >= 1, 'Precondition [rateLimit >= 1] failed');
        \assert($subject !== '', 'Precondition [subject !== ""] failed');
        \assert($stream !== '', 'Precondition [stream !== ""] failed');
    }

    /**
     * @psalm-return array{
     *     name: non-empty-string,
     *     driver: DriverType,
     *     priority: positive-int,
     *     prefetch: positive-int,
     *     subject: non-empty-string,
     *     deliver_new: bool,
     *     rate_limit: positive-int,
     *     stream: non-empty-string,
     *     delete_stream_on_stop: bool,
     *     delete_after_ack: bool
     * }
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch'              => $this->prefetch,
            'subject'               => $this->subject,
            'deliver_new'           => $this->deliverNew,
            'rate_limit'            => $this->rateLimit,
            'stream'                => $this->stream,
            'delete_stream_on_stop' => $this->deleteStreamOnStop,
            'delete_after_ack'      => $this->deleteAfterAck,
        ]);
    }
}
