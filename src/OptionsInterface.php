<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public const DEFAULT_PRIORITY = 10;

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
}
