<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\WorkerInterface;

/**
 * @psalm-import-type PartitionOffsetEnum from PartitionOffset
 * @psalm-suppress MutableDependency, MissingImmutableAnnotation
 */
final class KafkaReceivedTask extends ReceivedTask
{
    /** @var non-empty-string */
    private string $topic;

    /** @var positive-int|0 $partition */
    private int $partition;

    /** @var PartitionOffsetEnum */
    private int $offset;

    /**
     * @param WorkerInterface $worker
     * @param non-empty-string $id
     * @param non-empty-string $queue
     * @param non-empty-string $job
     * @param non-empty-string $topic
     * @param positive-int|0 $partition
     * @param PartitionOffsetEnum $offset
     * @param array $payload
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
        array $payload = [],
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
     * @return PartitionOffsetEnum
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
