<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the SQS driver.
 *
 * TODO An "attributes" and "tags" configuration sections not available yet.
 *      Should be added in future releases.
 *
 * @psalm-import-type CreateInfoArrayType from CreateInfoInterface
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
     * @var non-empty-string
     */
    public const QUEUE_DEFAULT_VALUE = 'default';

    /**
     * @var positive-int
     */
    public int $prefetch = self::PREFETCH_DEFAULT_VALUE;

    /**
     * @var positive-int|0
     */
    public int $visibilityTimeout = self::VISIBILITY_TIMEOUT_DEFAULT_VALUE;

    /**
     * @var positive-int|0
     */
    public int $waitTimeSeconds = self::WAIT_TIME_SECONDS_DEFAULT_VALUE;

    /**
     * @var non-empty-string
     */
    public string $queue = self::QUEUE_DEFAULT_VALUE;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     * @param positive-int|0 $visibilityTimeout
     * @param positive-int|0 $waitTimeSeconds
     * @param non-empty-string $queue
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        int $prefetch = self::PREFETCH_DEFAULT_VALUE,
        int $visibilityTimeout = self::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
        int $waitTimeSeconds = self::WAIT_TIME_SECONDS_DEFAULT_VALUE,
        string $queue = self::QUEUE_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::EPHEMERAL, $name, $priority);

        assert($prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        assert($visibilityTimeout >= 0, 'Precondition [visibilityTimeout >= 0] failed');
        assert($waitTimeSeconds >= 0, 'Precondition [waitTimeSeconds >= 0] failed');
        assert($queue !== '', 'Precondition [queue !== ""] failed');

        $this->prefetch = $prefetch;
        $this->visibilityTimeout = $visibilityTimeout;
        $this->waitTimeSeconds = $waitTimeSeconds;
        $this->queue = $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch'           => $this->prefetch,
            'visibility_timeout' => $this->visibilityTimeout,
            'wait_time_seconds'  => $this->waitTimeSeconds,
            'queue'              => $this->queue,
        ]);
    }
}
