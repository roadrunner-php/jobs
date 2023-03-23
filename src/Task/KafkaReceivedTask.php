<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\WorkerInterface;

/**
 * @psalm-suppress MutableDependency, MissingImmutableAnnotation
 */
final class KafkaReceivedTask extends ReceivedTask
{
    /** @var non-empty-string */
    private string $topic;

    /** @var positive-int|0 $partition */
    private int $partition;

    /** @var value-of<PartitionOffset> */
    private int $offset;

    /**
     * @param WorkerInterface $worker
     * @param non-empty-string $id
     * @param non-empty-string $queue
     * @param non-empty-string $job
     * @param non-empty-string $topic
     * @param int<0, max> $partition
     * @param value-of<PartitionOffset> $offset
     * @param string $payload
     * @param array<non-empty-string, array<string>> $headers
     */
    public function __construct(
        WorkerInterface $worker,
        string $id,
        string $queue,
        string $job,
        string $topic,
        int $partition,
        int $offset,
        string $payload = '',
        array $headers = []
    ) {
        $this->topic = $topic;
        $this->partition = $partition;
        $this->offset = $offset;

        parent::__construct($worker, $id, $queue, $job, $payload, $headers);
    }

    /**
     * @return non-empty-string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @return positive-int|0
     */
    public function getPartition(): int
    {
        return $this->partition;
    }

    /**
     * @return value-of<PartitionOffset>
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
