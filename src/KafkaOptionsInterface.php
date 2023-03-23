<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;

interface KafkaOptionsInterface extends OptionsInterface
{
    /**
     * @var string
     */
    public const DEFAULT_METADATA = '';

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
     */
    public function getMetadata(): string;

    /**
     * @psalm-immutable
     */
    public function getOffset(): PartitionOffset;

    /**
     * @psalm-immutable
     * @return positive-int|0
     */
    public function getPartition(): int;
}
