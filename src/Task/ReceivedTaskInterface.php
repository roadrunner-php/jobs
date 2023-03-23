<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\Exception\JobsException;

/**
 * @psalm-suppress MissingImmutableAnnotation The implementation of this task is mutable.
 */
interface ReceivedTaskInterface extends
    QueuedTaskInterface,
    WritableHeadersInterface,
    MutatesDelayInterface
{
    /**
     * Marks the current task as completed.
     *
     * @throws JobsException
     */
    public function complete(): void;

    /**
     * Marks the current task as failed.
     *
     * @param string|\Stringable|\Throwable $error
     * @throws JobsException
     */
    public function fail(string|\Stringable|\Throwable $error, bool $requeue = false): void;

    /**
     * Returns bool {@see true} if the task is completed and {@see false}
     * otherwise.
     *
     * @psalm-mutation-free
     */
    public function isCompleted(): bool;

    /**
     * Returns bool {@see true} if the task is successful completed
     * and {@see false} otherwise.
     *
     * @psalm-mutation-free
     */
    public function isSuccessful(): bool;

    /**
     * Returns bool {@see true} if the task has been failed and {@see false}
     * otherwise.
     *
     * @psalm-mutation-free
     */
    public function isFails(): bool;
}
