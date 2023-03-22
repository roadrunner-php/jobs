<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use JetBrains\PhpStorm\ArrayShape;

/**
 * The DTO to create the SQS driver.
 *
 * @psalm-type SQSAttributesMap = array {
 *      DelaySeconds?: positive-int|0,
 *      MaximumMessageSize?: positive-int|0,
 *      MessageRetentionPeriod?: positive-int|0,
 *      Policy?: mixed,
 *      ReceiveMessageWaitTimeSeconds?: positive-int|0,
 *      RedrivePolicy?: array {
 *          deadLetterTargetArn?: mixed,
 *          maxReceiveCount: positive-int|0
 *      },
 *      VisibilityTimeout?: positive-int|0,
 *      KmsMasterKeyId?: string,
 *      KmsDataKeyReusePeriodSeconds?: positive-int|0,
 *      ContentBasedDeduplication?: mixed,
 *      DeduplicationScope?: mixed,
 *      FifoThroughputLimit?: mixed,
 * }
 */
final class SQSCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const PREFETCH_DEFAULT_VALUE = 10;

    /**
     * @var positive-int|0
     */
    public const VISIBILITY_TIMEOUT_DEFAULT_VALUE = 0;

    /**
     * @var positive-int|0
     */
    public const WAIT_TIME_SECONDS_DEFAULT_VALUE = 0;

    /**
     * @var array
     */
    public const ATTRIBUTES_DEFAULT_VALUE = [];

    /**
     * @var array
     */
    public const TAGS_DEFAULT_VALUE = [];

    /**
     * @var non-empty-string
     */
    public const QUEUE_DEFAULT_VALUE = 'default';

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     * @param int<0, max> $visibilityTimeout
     * @param int<0, max> $waitTimeSeconds
     * @param non-empty-string $queue
     * @param array|SQSAttributesMap $attributes
     * @param array<non-empty-string, non-empty-string> $tags
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE,
        public readonly int $visibilityTimeout = self::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
        public readonly int $waitTimeSeconds = self::WAIT_TIME_SECONDS_DEFAULT_VALUE,
        public readonly string $queue = self::QUEUE_DEFAULT_VALUE,
        public readonly array $attributes = self::ATTRIBUTES_DEFAULT_VALUE,
        public readonly array $tags = self::TAGS_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::SQS, $name, $priority);

        \assert($this->prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        \assert($this->visibilityTimeout >= 0, 'Precondition [visibilityTimeout >= 0] failed');
        \assert($this->waitTimeSeconds >= 0, 'Precondition [waitTimeSeconds >= 0] failed');
        \assert($this->queue !== '', 'Precondition [queue !== ""] failed');
    }

    public function toArray(): array
    {
        $result = \array_merge(parent::toArray(), [
            'prefetch'           => $this->prefetch,
            'visibility_timeout' => $this->visibilityTimeout,
            'wait_time_seconds'  => $this->waitTimeSeconds,
            'queue'              => $this->queue,
        ]);
        if ($this->attributes !== []) {
            $result['attributes'] = $this->attributes;
        }
        if ($this->tags !== []) {
            $result['tags'] = $this->tags;
        }

        return $result;
    }
}
