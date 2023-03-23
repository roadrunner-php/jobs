<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Exception\ReceivedTaskException;
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\WorkerInterface;

/**
 * Note that the implementation of this class depends on the environment setting
 * of the RoadRunner. In the case that the current worker does NOT meet the
 * queue processing tasks, then the Consumer class is not guaranteed to work.
 *
 * In most cases, it will be enough for you to check the availability of the
 * environment parameter:
 *
 * <code>
 *  use Spiral\RoadRunner\Environment;
 *  use Spiral\RoadRunner\Environment\Mode;
 *
 *  $env = Environment::fromGlobals();
 *  if ($env->getMode() !== Mode::MODE_JOBS) {
 *     throw new RuntimeException('Can not create Jobs Consumer');
 *  }
 *
 *  $consumer = new Consumer(...);
 * </code>
 *
 * @psalm-type HeaderPayload = array {
 *    id:       non-empty-string,
 *    job:      non-empty-string,
 *    headers:  array<string, array<string>>|null,
 *    timeout:  positive-int,
 *    pipeline: non-empty-string
 * }
 */
final class Consumer implements ConsumerInterface
{
    private readonly WorkerInterface $worker;

    public function __construct(WorkerInterface $worker = null)
    {
        $this->worker = $worker ?? Worker::create();
    }

    /**
     * @throws ReceivedTaskException
     * @throws SerializationException
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function waitTask(): ?ReceivedTaskInterface
    {
        $payload = $this->worker->waitPayload();

        if ($payload === null) {
            return null;
        }

        $header = $this->getHeader($payload);

        return new ReceivedTask(
            $this->worker,
            $header['id'],
            $header['pipeline'],
            $header['job'],
            $payload->body,
            (array)$header['headers']
        );
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return HeaderPayload
     * @throws ReceivedTaskException
     * @throws SerializationException
     */
    private function getHeader(Payload $payload): array
    {
        if (empty($payload->header)) {
            throw new ReceivedTaskException('Task payload does not have a valid header.');
        }

        try {
            return (array)\json_decode($payload->header, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new SerializationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
