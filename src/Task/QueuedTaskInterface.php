<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

/**
 * @psalm-immutable
 * @psalm-allow-private-mutation
 */
interface QueuedTaskInterface extends TaskInterface
{
    /**
     * Returns the unique identifier of the task in the queue.
     *
     * @psalm-immutable
     * @return non-empty-string
     */
    public function getId(): string;
}
