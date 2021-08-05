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
 *
 * @psalm-type CreateInfoArrayType = array {
 *  name: non-empty-string,
 *  driver: DriverType,
 *  priority: positive-int
 * }
 */
interface CreateInfoInterface
{
    /**
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * @return DriverType
     */
    public function getDriver(): string;

    /**
     * @return CreateInfoArrayType
     */
    public function toArray(): array;
}
