<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\WorkerInterface;

final class KafkaReceivedTaskTest extends TestCase
{
    public function testGetTopic(): void
    {
        $task = new KafkaReceivedTask($this->createMock(WorkerInterface::class), '', '', '', 'foo', 0, 0);

        $this->assertSame('foo', $task->getTopic());
    }

    public function testGetPartition(): void
    {
        $task = new KafkaReceivedTask($this->createMock(WorkerInterface::class), '', '', '', 'foo', 5, 0);

        $this->assertSame(5, $task->getPartition());
    }

    public function testGetOffset(): void
    {
        $task = new KafkaReceivedTask($this->createMock(WorkerInterface::class), '', '', '', 'foo', 0, 3);

        $this->assertSame(3, $task->getOffset());
    }
}
