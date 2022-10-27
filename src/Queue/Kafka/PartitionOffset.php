<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

/**
 * The enum that represents the offsets for the partitions.
 *
 * @psalm-type PartitionOffsetEnum = PartitionOffset::OFFSET_*
 */
interface PartitionOffset
{
    /**
     * Stands for the log head offset, i.e. the offset that will be assigned to the next message
     * that will be produced to the partition.
     *
     * @var int
     * @psalm-var PartitionOffsetEnum
     */
    public const OFFSET_NEWEST = -1;

    /**
     * Stands for the oldest offset available on the broker for a partition.
     *
     * @var int
     * @psalm-var PartitionOffsetEnum
     */
    public const OFFSET_OLDEST = -2;
}
