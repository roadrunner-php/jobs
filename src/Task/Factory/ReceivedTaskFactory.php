<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task\Factory;

use Spiral\RoadRunner\Jobs\Exception\ReceivedTaskException;
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

/**
 * @psalm-import-type PartitionOffsetEnum from PartitionOffset
 * @psalm-type HeaderPayload = array {
 *    id:         non-empty-string,
 *    job:        non-empty-string,
 *    headers:    array<string, array<string>>|null,
 *    timeout:    positive-int,
 *    pipeline:   non-empty-string,
 *    driver?:    non-empty-string,
 *    topic:      non-empty-string,
 *    partition:  positive-int,
 *    offset:     PartitionOffsetEnum
 * }
 */
final class ReceivedTaskFactory implements ReceivedTaskFactoryInterface
{
    public function __construct(
        private readonly WorkerInterface $worker,
    ) {
    }

    /**
     * @throws SerializationException
     * @throws ReceivedTaskException
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function create(Payload $payload): ReceivedTaskInterface
    {
        $header = $this->getHeader($payload);

        return match ($header['driver'] ?? null) {
            Driver::Kafka->value => new KafkaReceivedTask(
                $this->worker,
                $header['id'],
                $header['pipeline'],
                $header['job'],
                $header['topic'],
                (int)$header['partition'],
                (int)$header['offset'],
                $payload->body,
                (array)$header['headers']
            ),
            default => new ReceivedTask(
                $this->worker,
                $header['id'],
                $header['pipeline'],
                $header['job'],
                $payload->body,
                (array)$header['headers']
            ),
        };
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @param Payload $payload
     * @return HeaderPayload
     * @throws SerializationException
     * @throws ReceivedTaskException
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
