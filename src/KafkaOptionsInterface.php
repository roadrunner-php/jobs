<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;

interface KafkaOptionsInterface extends OptionsInterface
{
    public const DEFAULT_METADATA = '';
    public const DEFAULT_PARTITION = 0;

    /**
     * @psalm-immutable
     * @return non-empty-string
     */
    public function getTopic(): string;

    /**
     * @psalm-immutable
     */
    public function getMetadata(): string;

    /**
     * @psalm-immutable
     */
    public function getOffset(): PartitionOffset;

    /**
     * @psalm-immutable
     * @return int<0, max>
     */
    public function getPartition(): int;
}
