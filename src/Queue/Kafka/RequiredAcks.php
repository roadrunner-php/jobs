<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

/**
 * The enum that represents the level of acknowledgement reliability needed from the broker.
 */
enum RequiredAcks: int
{
    /**
     * Doesn't send any response.
     */
    case TypeNoResponse = 0;

    /**
     * Waits for only the local commit to succeed before responding.
     */
    case TypeWaitForLocal = 1;

    /**
     * Waits for all in-sync replicas to commit before responding.
     */
    case TypeWaitForAll = -1;
}
