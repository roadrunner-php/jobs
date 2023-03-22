<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the Boltdb driver.
 *
 * @psalm-import-type DriverType from Driver
 */
final class BoltdbCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const PREFETCH_DEFAULT_VALUE = 10000;

    /**
     * @var non-empty-string
     */
    public const FILE_DEFAULT_VALUE = 'rr.db';

    /**
     * @param non-empty-string $name
     * @param non-empty-string $file
     * @param positive-int $priority
     * @param positive-int $prefetch
     */
    public function __construct(
        string $name,
        public readonly string $file = self::FILE_DEFAULT_VALUE,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $prefetch = self::PREFETCH_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::BOLTDB, $name, $priority);

        \assert($prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        \assert($file !== '', 'Precondition [file !== ""] failed');
    }

    /**
     * @psalm-return array{
     *     name: non-empty-string,
     *     driver: DriverType,
     *     priority: positive-int,
     *     prefetch: positive-int,
     *     file: non-empty-string
     * }
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch' => $this->prefetch,
            'file' => $this->file,
        ]);
    }
}
