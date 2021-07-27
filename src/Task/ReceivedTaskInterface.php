<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\Exception\JobsException;

/**
 * @psalm-suppress MissingImmutableAnnotation The implementation of this task is mutable.
 */
interface ReceivedTaskInterface extends QueuedTaskInterface
{
    /**
     * Marks the current task as completed.
     *
     * @return void
     * @throws JobsException
     */
    public function complete(): void;

    /**
     * Returns bool {@see true} if the task is completed and {@see false}
     * otherwise.
     *
     * @return bool
     */
    public function isCompleted(): bool;
}
