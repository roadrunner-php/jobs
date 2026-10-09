<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Testo\Test;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\WorkerInterface;

#[Test]
final class KafkaReceivedTaskTest
{
    public function testGetsDriver(): void
    {
        $task = $this->createTask();
        Assert::equals($task->getDriver(), Driver::Kafka);
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
            $this->worker,
            $id,
            $pipeline,
            $name,
            $queue,
            $partition,
            $offset,
            $payload,
            $headers,
        );
    }

    public function testGetsQueue(): void
    {
        $task = $this->createTask(queue: 'kafka-queue-name');

        Assert::same($task->getQueue(), 'kafka-queue-name');
    }

    public function testGetsPartition(): void
    {
        $task = $this->createTask(partition: 1);
        Assert::same($task->getPartition(), 1);


        $task = $this->createTask(partition: 100);
        Assert::same($task->getPartition(), 100);
    }

    public function testGetsOffset(): void
    {
        $task = $this->createTask(offset: 1);
        Assert::same($task->getOffset(), 1);

        $task = $this->createTask(offset: 100);
        Assert::same($task->getOffset(), 100);
    }

    public function testGetsPipeline(): void
    {
        $task = $this->createTask(pipeline: 'custom');
        Assert::same($task->getPipeline(), 'custom');
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->worker = \Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing();
    }
}
