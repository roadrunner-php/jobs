<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the Memory driver.
 *
 * @psalm-import-type DriverType from Driver
 */
final class MemoryCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const PREFETCH_DEFAULT_VALUE = 10;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::MEMORY, $name, $priority);

        \assert($this->prefetch >= 1, 'Precondition [prefetch >= 1] failed');
    }

    /**
     * @return array{
     *     name: non-empty-string,
     *     driver: DriverType,
     *     priority: int<1, max>,
     *     prefetch: int<1, max>
     * }
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch' => $this->prefetch,
        ]);
    }
}
