<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * @psalm-import-type DriverType from Driver
 */
class CreateInfo implements CreateInfoInterface
{
    /**
     * @var positive-int
     */
    public const PRIORITY_DEFAULT_VALUE = 10;

    /**
     * @param DriverType $driver
     * @param non-empty-string $name
     * @param positive-int $priority Queue default priority for for each task pushed into this queue if the
     * priority value for these tasks was not explicitly set.
     */
    public function __construct(
        public readonly string $driver,
        public readonly string $name,
        public readonly int $priority = self::PRIORITY_DEFAULT_VALUE
    ) {
        \assert($this->driver !== '', 'Precondition [driver !== ""] failed');
        \assert($this->name !== '', 'Precondition [name !== ""] failed');
        \assert($this->priority >= 1, 'Precondition [priority >= 1] failed');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'driver' => $this->driver,
            'priority' => $this->priority,
        ];
    }
}
