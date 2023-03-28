<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\WorkerInterface;

final class KafkaReceivedTaskTest extends TestCase
{
    public function testGetsDriver(): void
    {
        $task = $this->createTask();
        $this->assertEquals(Driver::Kafka, $task->getDriver());
    }

    public function createTask(
        string $id = '12345',
        string $pipeline = 'default',
        string $queue = 'default',
        string $name = 'TestTask',
        int $partition = 0,
        int $offset = 0,
        string $payload = 'foo=bar',
        array $headers = [],
    ): KafkaReceivedTask {
        return new KafkaReceivedTask(
            $this->worker, $id, $pipeline, $name, $queue, $partition, $offset, $payload, $headers
        );
    }

    public function testGetsQueue(): void
    {
        $task = $this->createTask(queue: 'kafka-queue-name');

        $this->assertSame('kafka-queue-name', $task->getQueue());
    }

    public function testGetsPartition(): void
    {
        $task = $this->createTask(partition: 1);
        $this->assertSame(1, $task->getPartition());


        $task = $this->createTask(partition: 100);
        $this->assertSame(100, $task->getPartition());
    }

    public function testGetsOffset(): void
    {
        $task = $this->createTask(offset: 1);
        $this->assertSame(1, $task->getOffset());

        $task = $this->createTask(offset: 100);
        $this->assertSame(100, $task->getOffset());
    }

    public function testGetsPipeline(): void
    {
        $task = $this->createTask(pipeline: 'custom');
        $this->assertSame('custom', $task->getPipeline());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->worker = $this->createMock(WorkerInterface::class);
    }
}
