<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

interface OptionsInterface
{
    /**
     * @var positive-int|0
     */
    public const DEFAULT_DELAY = 0;

    /**
     * @var positive-int|0
     */
    public const DEFAULT_PRIORITY = 0;

    /**
     * @var bool
     */
    public const DEFAULT_AUTO_ACK = false;

    /**
     * @psalm-immutable
     * @return positive-int|0
     */
    public function getDelay(): int;

    /**
     * @psalm-immutable
     * @return positive-int|0
     */
    public function getPriority(): int;

    /**
     * @psalm-immutable
     */
    public function getAutoAck(): bool;
}
