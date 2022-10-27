<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;

/**
 * @psalm-import-type PartitionOffsetEnum from PartitionOffset
 */
interface KafkaOptionsInterface extends OptionsInterface
{
    /**
     * @var string
     */
    public const DEFAULT_METADATA = '';

    /**
     * @var PartitionOffsetEnum
     */
    public const DEFAULT_OFFSET = PartitionOffset::OFFSET_NEWEST;

    /**
     * @var positive-int|0
     */
    public const DEFAULT_PARTITION = 0;

    /**
     * @psalm-immutable
     * @return non-empty-string
     */
    public function getTopic(): string;

    /**
     * @psalm-immutable
     * @return string
     */
    public function getMetadata(): string;

    /**
     * @psalm-immutable
     * @return PartitionOffsetEnum
     */
    public function getOffset(): int;

    /**
     * @psalm-immutable
     * @return positive-int|0
     */
    public function getPartition(): int;
}
