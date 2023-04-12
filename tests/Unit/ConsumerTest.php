<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactoryInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

final class ConsumerTest extends \PHPUnit\Framework\TestCase
{
    private Consumer $consumer;
    private WorkerInterface|MockObject $woker;
    private ReceivedTaskFactoryInterface|MockObject $factory;

    public function testReceivedTask(): void
    {
        $this->woker->method('waitPayload')->willReturn(
            $payload = new Payload(
                'foo',
                \json_encode([
                    'id' => 'job-id',
                    'queue' => 'job-queue',
                    'driver' => 'memory',
                    'pipeline' => 'job-pipeline',
                    'job' => 'job-name',
                    'headers' => ['foo' => 'bar'],
                ])
            ),
        );

        $this->factory->method('create')
            ->with($payload)
            ->willReturn($task = $this->createMock(ReceivedTaskInterface::class));

        $this->assertSame($task, $this->consumer->waitTask());
    }

    public function testEmptyPayload(): void
    {
        $this->woker->method('waitPayload')->willReturn(null);
        $this->factory->expects($this->never())->method('create');

        $this->assertNull($this->consumer->waitTask());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->consumer = new Consumer(
            $this->woker = $this->createMock(WorkerInterface::class),
            $this->factory = $this->createMock(ReceivedTaskFactoryInterface::class),
        );
    }
}
