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
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

/**
 * @psalm-type SuccessData = array
 * @psalm-type ErrorData = array { message: string, requeue: bool, delay_seconds: positive-int|0 }
 *
 * @psalm-import-type TypeEnum from Type
 *
 * @psalm-suppress MissingImmutableAnnotation The implementation of this task is mutable.
 */
final class ReceivedTask extends QueuedTask implements ReceivedTaskInterface
{
    /**
     * @var WorkerInterface
     */
    private WorkerInterface $worker;

    /**
     * @var TypeEnum|null
     */
    private ?int $completed = null;

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

    /**
     * {@inheritDoc}
     */
    public function complete(): void
    {
        $this->respond(Type::SUCCESS);
    }

    /**
     * @param TypeEnum $type
     * @param SuccessData|ErrorData $data
     * @return void
     * @throws JobsException
     */
    private function respond(int $type, array $data = []): void
    {
        if ($this->completed === null) {
            try {
                $body = \json_encode(['type' => $type, 'data' => $data], \JSON_THROW_ON_ERROR);

                $this->worker->respond(new Payload($body));
            } catch (\JsonException $e) {
                throw new SerializationException($e->getMessage(), (int)$e->getCode(), $e);
            } catch (\Throwable $e) {
                throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
            }

            $this->completed = $type;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fail(string $message, bool $requeue = true, int $delay = 0): void
    {
        $this->respond(Type::ERROR, [
            'message'       => $message,
            'requeue'       => $requeue,
            'delay_seconds' => $delay,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function isCompleted(): bool
    {
        return $this->completed !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function isSuccessful(): bool
    {
        return $this->completed === Type::SUCCESS;
    }

    /**
     * {@inheritDoc}
     */
    public function isFails(): bool
    {
        return $this->completed === Type::ERROR;
    }
}
