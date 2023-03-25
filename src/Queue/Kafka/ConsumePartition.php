<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

final class ConsumePartition
{
    public function __construct(
        public readonly string $topic,
        public readonly int $partition,
        public readonly ?ConsumerOffset $offset = null,
    ) {
    }
}