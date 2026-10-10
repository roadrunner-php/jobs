<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Mockery\MockInterface;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactoryInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class ConsumerTest
{
    private Consumer $consumer;
    private WorkerInterface|MockInterface $woker;
    private ReceivedTaskFactoryInterface|MockInterface $factory;

    public function testReceivedTask(): void
    {
        $this->woker->shouldReceive('waitPayload')->andReturn($payload = new Payload(
            'foo',
            \json_encode([
                'id' => 'job-id',
                'queue' => 'job-queue',
                'driver' => 'memory',
                'pipeline' => 'job-pipeline',
                'job' => 'job-name',
                'headers' => ['foo' => 'bar'],
            ]),
        ));

        $this->factory->shouldReceive('create')->with($payload, \Mockery::andAnyOtherArgs())->andReturn($task = \Mockery::mock(ReceivedTaskInterface::class)->shouldIgnoreMissing());

        Assert::same($this->consumer->waitTask(), $task);
    }

    public function testEmptyPayload(): void
    {
        $this->woker->shouldReceive('waitPayload')->andReturn(null);
        $this->factory->shouldReceive('create')->never();

        Assert::null($this->consumer->waitTask());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->consumer = new Consumer(
            $this->woker = \Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing(),
            $this->factory = \Mockery::mock(ReceivedTaskFactoryInterface::class)->shouldIgnoreMissing(),
        );
    }
}
