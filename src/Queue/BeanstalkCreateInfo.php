<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * The DTO to create the Beanstalk driver.
 *
 * @psalm-import-type DriverType from Driver
 */
final class BeanstalkCreateInfo extends CreateInfo
{
    /**
     * @var positive-int
     */
    public const TUBE_PRIORITY_DEFAULT_VALUE = 10;

    /**
     * @var positive-int
     */
    public const TUBE_PRIORITY_MAX_VALUE = 2 ** 32;

    /**
     * @var non-empty-string
     */
    public const TUBE_DEFAULT_VALUE = 'default';

    /**
     * @var positive-int|0
     */
    public const RESERVE_TIMEOUT_DEFAULT_VALUE = 5;

    /**
     * @param non-empty-string $name
     * @param positive-int $priority
     * @param positive-int $tubePriority
     * @param non-empty-string $tube
     * @param positive-int|0 $reserveTimeout
     */
    public function __construct(
        string $name,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly int $tubePriority = self::TUBE_PRIORITY_DEFAULT_VALUE,
        public readonly string $tube = self::TUBE_DEFAULT_VALUE,
        public readonly int $reserveTimeout = self::RESERVE_TIMEOUT_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::BEANSTALK, $name, $priority);

        \assert($this->tubePriority >= 1, 'Precondition [tubePriority >= 1] failed');
        \assert($this->tube !== '', 'Precondition [tube !== ""] failed');
        \assert($this->reserveTimeout >= 0, 'Precondition [reserveTimeout >= 0] failed');
    }

    /**
     * @return array{
     *     name: non-empty-string,
     *     driver: DriverType,
     *     priority: positive-int,
     *     tube_priority: positive-int,
     *     tube: non-empty-string,
     *     reserve_timeout: int<0, max>
     * }
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'tube_priority'   => $this->tubePriority,
            'tube'            => $this->tube,
            'reserve_timeout' => $this->reserveTimeout,
        ]);
    }
}
