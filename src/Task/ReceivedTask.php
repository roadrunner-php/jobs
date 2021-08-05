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
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

/**
 * @psalm-suppress MissingImmutableAnnotation The implementation of this task is mutable.
 */
final class ReceivedTask extends QueuedTask implements ReceivedTaskInterface
{
    /**
     * @var WorkerInterface
     */
    private WorkerInterface $worker;

    /**
     * @var bool
     */
    private bool $completed = false;

    /**
     * @param WorkerInterface $worker
     * @param non-empty-string $id
     * @param non-empty-string $queue
     * @param non-empty-string $job
     * @param array $payload
     * @param array<non-empty-string, array<string>> $headers
     */
    public function __construct(
        WorkerInterface $worker,
        string $id,
        string $queue,
        string $job,
        array $payload = [],
        array $headers = []
    ) {
        $this->worker = $worker;

        parent::__construct($id, $queue, $job, $payload, $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function complete(): void
    {
        if ($this->completed === false) {
            try {
                $this->worker->respond(new Payload('jobs.complete:' . $this->id));
            } catch (\Throwable $e) {
                throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
            }

            $this->completed = true;
        }
    }

    /**
     * @return void
     */
    public function fail(): void
    {
        $this->worker->error('jobs.fail:' . $this->id);
    }

    /**
     * {@inheritDoc}
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        // This is a little sugar so that the status of the
        // current task is not lost.
        //
        // Everything will be fine even if the task has already been marked
        // as "completed": The current method will simply not be executed.
        try {
            $this->complete();
        } catch (JobsException $e) {
            // Suppress shutdown exception
        }
    }
}
