<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the Boltdb driver.
 *
 * @psalm-import-type CreateInfoArrayType from CreateInfoInterface
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
     * @var positive-int
     */
    public int $prefetch = self::PREFETCH_DEFAULT_VALUE;

    /**
     * @var non-empty-string
     */
    public string $file = self::FILE_DEFAULT_VALUE;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $prefetch
     */
    public function __construct(
        string $name,
        string $file = self::FILE_DEFAULT_VALUE,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        int $prefetch = self::PREFETCH_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::BOLTDB, $name, $priority);

        assert($prefetch >= 1, 'Precondition [prefetch >= 1] failed');
        assert($file !== '', 'Precondition [file !== ""] failed');

        $this->prefetch = $prefetch;
        $this->file = $file;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'prefetch' => $this->prefetch,
            'file' => $this->file,
        ]);
    }
}
