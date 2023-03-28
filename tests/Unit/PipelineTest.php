<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use RoadRunner\Jobs\DTO\V1\Options as DTOOptions;
use RoadRunner\Jobs\DTO\V1\PushBatchRequest;
use RoadRunner\Jobs\DTO\V1\PushRequest;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Queue\Pipeline;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;
use Traversable;

final class PipelineTest extends TestCase
{
    /** @dataProvider taskToProtoDataProvider */
    public function testSend(OptionsInterface $options, DTOOptions $expected): void
    {
        $pipeline = new Pipeline(
            'foo',
            $rpc = $this->createMock(RPCInterface::class),
            $uuid = $this->createMock(UuidFactoryInterface::class),
        );

        $uuid->method('uuid4')->willReturn($uuid = Uuid::uuid4());

        $rpc->method('call')->with(
            'jobs.Push',
            $this->callback(function (PushRequest $request) use ($expected, $uuid) {
                return $request->getJob()->getJob() === 'bar'
                    && $request->getJob()->getId() === (string)$uuid
                    && $request->getJob()->getPayload() === 'foo=bar'
                    && $request->getJob()->getHeaders()->count() === 0
                    && \json_encode($request->getJob()->getOptions()) === \json_encode($expected);
            }),
            null,
        );

        $queuedTask = $pipeline->send(new PreparedTask('bar', 'foo=bar', $options));

        $this->assertSame((string)$uuid, $queuedTask->getId());
        $this->assertSame('bar', $queuedTask->getName());
        $this->assertSame('foo', $queuedTask->getPipeline());
        $this->assertSame('foo=bar', $queuedTask->getPayload());
        $this->assertSame([], $queuedTask->getHeaders());
    }

    /** @dataProvider taskToProtoDataProvider */
    public function testSendMany(OptionsInterface $options, DTOOptions $expected): void
    {
        $pipeline = new Pipeline(
            'foo',
            $rpc = $this->createMock(RPCInterface::class),
            $uuid = $this->createMock(UuidFactoryInterface::class),
        );

        $uuid->method('uuid4')->willReturn($uuid1 = Uuid::uuid4(), $uuid2 = Uuid::uuid4());

        $rpc->method('call')->with(
            'jobs.PushBatch',
            $this->callback(function (PushBatchRequest $request) use ($expected, $uuid1, $uuid2) {
                return $request->getJobs()->count() === 2
                    && $request->getJobs()->offsetGet(0)->getJob() === 'bar'
                    && $request->getJobs()->offsetGet(0)->getId() === (string)$uuid1
                    && $request->getJobs()->offsetGet(0)->getPayload() === 'foo=bar'
                    && $request->getJobs()->offsetGet(1)->getJob() === 'baz'
                    && $request->getJobs()->offsetGet(1)->getId() === (string)$uuid2
                    && $request->getJobs()->offsetGet(1)->getPayload() === 'foo=bar1';
            }),
            null,
        );

        $queuedTasks = $pipeline->sendMany([
            new PreparedTask('bar', 'foo=bar', $options),
            new PreparedTask('baz', 'foo=bar1', $options),
        ]);

        $this->assertCount(2, $queuedTasks);

        $this->assertSame((string)$uuid1, $queuedTasks[0]->getId());
        $this->assertSame('bar', $queuedTasks[0]->getName());
        $this->assertSame('foo', $queuedTasks[0]->getPipeline());
        $this->assertSame('foo=bar', $queuedTasks[0]->getPayload());
        $this->assertSame([], $queuedTasks[0]->getHeaders());

        $this->assertSame((string)$uuid2, $queuedTasks[1]->getId());
        $this->assertSame('baz', $queuedTasks[1]->getName());
        $this->assertSame('foo', $queuedTasks[1]->getPipeline());
        $this->assertSame('foo=bar1', $queuedTasks[1]->getPayload());
        $this->assertSame([], $queuedTasks[1]->getHeaders());
    }

    public function testSendManyWithError(): void
    {
        $this->expectException(JobsException::class);
        $this->expectExceptionMessage('Some error');

        $pipeline = new Pipeline(
            'foo',
            $rpc = $this->createMock(RPCInterface::class),
        );

        $rpc->method('call')->willThrowException(new \Exception('Some error'));

        $pipeline->sendMany([
            new PreparedTask('bar', 'foo=bar'),
            new PreparedTask('baz', 'foo=bar1'),
        ]);
    }

    public function testSendWithError(): void
    {
        $this->expectException(JobsException::class);
        $this->expectExceptionMessage('Some error');

        $pipeline = new Pipeline(
            'foo',
            $rpc = $this->createMock(RPCInterface::class),
        );

        $rpc->method('call')->willThrowException(new \Exception('Some error'));

        $pipeline->send(new PreparedTask('bar', 'foo=bar'));
    }

    public function taskToProtoDataProvider(): Traversable
    {
        yield [
            new Options(5, 10),
            new DTOOptions([
                'priority' => 10,
                'pipeline' => '',
                'delay' => 5,
                'auto_ack' => false,
                'topic' => '',
                'metadata' => '',
            ]),
        ];

        yield [
            new KafkaOptions('some', 10, 5, true, 'other', 1, 7),
            new DTOOptions([
                'priority' => 5,
                'pipeline' => '',
                'delay' => 10,
                'auto_ack' => true,
                'topic' => 'some',
                'metadata' => 'other',
                'offset' => 1,
                'partition' => 7,
            ]),
        ];
    }
}
