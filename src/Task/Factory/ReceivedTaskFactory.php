<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task\Factory;

use Spiral\RoadRunner\Jobs\Exception\ReceivedTaskException;
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\Jobs\Serializer\SerializerAwareInterface;
use Spiral\RoadRunner\Jobs\Serializer\SerializerInterface;
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
final class ReceivedTaskFactory implements ReceivedTaskFactoryInterface, SerializerAwareInterface
{
    private WorkerInterface $worker;
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, WorkerInterface $worker)
    {
        $this->serializer = $serializer;
        $this->worker = $worker;
    }

    /**
     * @throws SerializationException
     * @throws ReceivedTaskException
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function create(Payload $payload): ReceivedTaskInterface
    {
        $header = $this->getHeader($payload);

        switch ($header['driver'] ?? null) {
            case Driver::KAFKA:
                return new KafkaReceivedTask(
                    $this->worker,
                    $header['id'],
                    $header['pipeline'],
                    $header['job'],
                    $header['topic'],
                    (int)$header['partition'],
                    (int)$header['offset'],
                    $this->getPayload($payload),
                    (array)$header['headers']
                );
            default:
                return new ReceivedTask(
                    $this->worker,
                    $header['id'],
                    $header['pipeline'],
                    $header['job'],
                    $this->getPayload($payload),
                    (array)$header['headers']
                );
        }
    }

    /**
     * @param Payload $payload
     * @return array
     * @throws SerializationException
     */
    private function getPayload(Payload $payload): array
    {
        if ($payload->body === '') {
            return [];
        }

        return $this->serializer->deserialize($payload->body);
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

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @return ReceivedTaskFactory
     */
    public function withSerializer(SerializerInterface $serializer): self
    {
        $self = clone $this;
        $self->serializer = $serializer;

        return $self;
    }
}
