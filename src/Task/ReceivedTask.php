<?php

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
    use WritableHeadersTrait;

    /**
     * @var TypeEnum|null
     */
    private ?int $completed = null;

    /**
     * @var positive-int|0
     */
    private int $delay = 0;

    /**
     * @param non-empty-string $id
     * @param non-empty-string $queue
     * @param non-empty-string $job
     * @param array<non-empty-string, array<string>> $headers
     */
    public function __construct(
        private readonly WorkerInterface $worker,
        string $id,
        string $queue,
        string $job,
        string $payload,
        array $headers = []
    ) {
        parent::__construct($id, $queue, $job, $payload, $headers);
    }

    public function complete(): void
    {
        $this->respond(Type::SUCCESS);
    }

    /**
     * @param TypeEnum $type
     * @param SuccessData|ErrorData $data
     * @throws JobsException
     */
    private function respond(int $type, array $data = []): void
    {
        if ($this->completed === null) {
            try {
                $body = \json_encode(['type' => $type, 'data' => $data], \JSON_THROW_ON_ERROR);

                $this->worker->respond(new Payload($body));
            } catch (\JsonException $e) {
                throw new SerializationException($e->getMessage(), $e->getCode(), $e);
            } catch (\Throwable $e) {
                throw new JobsException($e->getMessage(), (int) $e->getCode(), $e);
            }

            $this->completed = $type;
        }
    }

    public function fail(string|\Stringable|\Throwable $error, bool $requeue = false): void
    {
        \assert(
            \is_string($error) || $error instanceof \Stringable,
            'Precondition [error is string|Stringable|Throwable] failed'
        );

        $data = [
            'message'       => (string)$error,
            'requeue'       => $requeue,
            'delay_seconds' => $this->delay,
        ];

        if (!empty($this->headers)) {
            $data['headers'] = $this->headers;
        }

        $this->respond(Type::ERROR, $data);
    }

    public function isCompleted(): bool
    {
        return $this->completed !== null;
    }

    public function isSuccessful(): bool
    {
        return $this->completed === Type::SUCCESS;
    }

    public function isFails(): bool
    {
        return $this->completed === Type::ERROR;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function withDelay(int $seconds): self
    {
        \assert($seconds >= 0, 'Precondition [seconds >= 0] failed');

        $self = clone $this;
        $self->delay = $seconds;

        return $self;
    }
}
