<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

/**
 * The enum that represents the level of acknowledgement reliability needed from the broker.
 *
 * @psalm-type RequiredAcksEnum = RequiredAcks::TYPE_*
 */
interface RequiredAcks
{
    /**
     * Doesn't send any response.
     *
     * @var int
     * @psalm-var RequiredAcksEnum
     */
    public const TYPE_NO_RESPONSE = 0;

    /**
     * Waits for only the local commit to succeed before responding.
     *
     * @var int
     * @psalm-var RequiredAcksEnum
     */
    public const TYPE_WAIT_FOR_LOCAL = 1;

    /**
     * Waits for all in-sync replicas to commit before responding.
     *
     * @var int
     * @psalm-var RequiredAcksEnum
     */
    public const TYPE_WAIT_FOR_ALL = -1;
}
