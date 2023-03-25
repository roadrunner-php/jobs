<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

class ConsumerOffset implements \JsonSerializable
{
    public function __construct(
        public readonly OffsetType $type,
        public readonly int $value,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'value' => $this->value,
        ];
    }
}