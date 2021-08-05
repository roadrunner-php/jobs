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
 * @psalm-import-type DriverType from Driver
 * @psalm-import-type CreateInfoArrayType from CreateInfoInterface
 */
class CreateInfo implements CreateInfoInterface
{
    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @var DriverType
     */
    public string $driver;

    /**
     * @var positive-int
     */
    public int $priority;

    /**
     * @param DriverType $driver
     * @param non-empty-string $name
     * @param positive-int $priority
     */
    public function __construct(string $driver, string $name, int $priority = 1)
    {
        $this->driver = $driver;
        $this->name = $name;
        $this->priority = $priority;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'driver' => $this->driver,
            'priority' => $this->priority,
        ];
    }
}
