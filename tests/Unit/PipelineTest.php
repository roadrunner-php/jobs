<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

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
use Spiral\RoadRunner\Jobs\Task\PreparedTaskInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Expect;
use Testo\Test;

#[Test]
final class PipelineTest
{
    public static function taskToProtoDataProvider(): \Traversable
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

    #[DataProvider('taskToProtoDataProvider')]
    public function testSend(OptionsInterface $options, DTOOptions $expected): void
    {
        $pipeline = new Pipeline(
            'foo',
            $rpc = \Mockery::mock(RPCInterface::class)->shouldIgnoreMissing(),
            $uuid = \Mockery::mock(UuidFactoryInterface::class)->shouldIgnoreMissing(),
        );

        $uuid->shouldReceive('uuid4')->andReturn($uuid = Uuid::uuid4());

        $rpc->shouldReceive('call')->once()->with(
            'jobs.Push',
            \Mockery::on(function (PushRequest $request) use ($expected, $uuid) {
                return $request->getJob()->getJob() === 'bar'
                    && $request->getJob()->getId() === (string) $uuid
                    && $request->getJob()->getPayload() === 'foo=bar'
                    && $request->getJob()->getHeaders()->count() === 0
                    && \json_encode($request->getJob()->getOptions()) === \json_encode($expected);
            }),
        );

        $queuedTask = $pipeline->send(new PreparedTask('bar', 'foo=bar', $options));

        Assert::same($queuedTask->getId(), (string) $uuid);
        Assert::same($queuedTask->getName(), 'bar');
        Assert::same($queuedTask->getPipeline(), 'foo');
        Assert::same($queuedTask->getPayload(), 'foo=bar');
        Assert::same($queuedTask->getHeaders(), []);
    }

    #[DataProvider('taskToProtoDataProvider')]
    public function testSendMany(OptionsInterface $options, DTOOptions $expected): void
    {
        $pipeline = new Pipeline(
            'foo',
            $rpc = \Mockery::mock(RPCInterface::class)->shouldIgnoreMissing(),
            $uuid = \Mockery::mock(UuidFactoryInterface::class)->shouldIgnoreMissing(),
        );

        $uuid->shouldReceive('uuid4')->andReturn($uuid1 = Uuid::uuid4(), $uuid2 = Uuid::uuid4());

        $rpc->shouldReceive('call')->once()->with(
            'jobs.PushBatch',
            \Mockery::on(function (PushBatchRequest $request) use ($expected, $uuid1, $uuid2) {
                return $request->getJobs()->count() === 2
                    && $request->getJobs()->offsetGet(0)->getJob() === 'bar'
                    && $request->getJobs()->offsetGet(0)->getId() === (string) $uuid1
                    && $request->getJobs()->offsetGet(0)->getPayload() === 'foo=bar'
                    && $request->getJobs()->offsetGet(1)->getJob() === 'baz'
                    && $request->getJobs()->offsetGet(1)->getId() === (string) $uuid2
                    && $request->getJobs()->offsetGet(1)->getPayload() === 'foo=bar1';
            }),
        );

        $queuedTasks = $pipeline->sendMany([
            new PreparedTask('bar', 'foo=bar', $options),
            new PreparedTask('baz', 'foo=bar1', $options),
        ]);

        Assert::count($queuedTasks, 2);

        Assert::same($queuedTasks[0]->getId(), (string) $uuid1);
        Assert::same($queuedTasks[0]->getName(), 'bar');
        Assert::same($queuedTasks[0]->getPipeline(), 'foo');
        Assert::same($queuedTasks[0]->getPayload(), 'foo=bar');
        Assert::same($queuedTasks[0]->getHeaders(), []);

        Assert::same($queuedTasks[1]->getId(), (string) $uuid2);
        Assert::same($queuedTasks[1]->getName(), 'baz');
        Assert::same($queuedTasks[1]->getPipeline(), 'foo');
        Assert::same($queuedTasks[1]->getPayload(), 'foo=bar1');
        Assert::same($queuedTasks[1]->getHeaders(), []);
    }

    public function testSendManyWithError(): void
    {
        Expect::exception(JobsException::class)->withMessageContaining('Some error');

        $pipeline = new Pipeline(
            'foo',
            $rpc = \Mockery::mock(RPCInterface::class)->shouldIgnoreMissing(),
        );

        $rpc->shouldReceive('call')->andThrow(new \Exception('Some error'));

        $pipeline->sendMany([
            new PreparedTask('bar', 'foo=bar'),
            new PreparedTask('baz', 'foo=bar1'),
        ]);
    }

    public function testSendWithError(): void
    {
        Expect::exception(JobsException::class)->withMessageContaining('Some error');

        $pipeline = new Pipeline(
            'foo',
            $rpc = \Mockery::mock(RPCInterface::class)->shouldIgnoreMissing(),
        );

        $rpc->shouldReceive('call')->andThrow(new \Exception('Some error'));

        $pipeline->send(new PreparedTask('bar', 'foo=bar'));
    }

    public function testSendRethrowsJobsExceptionAsIs(): void
    {
        $pipeline = new Pipeline('foo', $rpc = \Mockery::mock(RPCInterface::class));
        $rpc->shouldReceive('call')->andThrow($exception = new JobsException('Queue is closed'));

        try {
            $pipeline->send(new PreparedTask('bar', 'foo=bar'));
            Assert::fail('JobsException was not thrown');
        } catch (JobsException $e) {
            Assert::same($e, $exception);
        }
    }

    public function testSendManyRethrowsJobsExceptionAsIs(): void
    {
        $pipeline = new Pipeline('foo', $rpc = \Mockery::mock(RPCInterface::class));
        $rpc->shouldReceive('call')->andThrow($exception = new JobsException('Queue is closed'));

        try {
            $pipeline->sendMany([new PreparedTask('bar', 'foo=bar')]);
            Assert::fail('JobsException was not thrown');
        } catch (JobsException $e) {
            Assert::same($e, $exception);
        }
    }

    public function testSendSkipsHeadersWithoutValues(): void
    {
        $pipeline = new Pipeline('foo', $rpc = \Mockery::mock(RPCInterface::class));

        $sentHeaders = null;
        $rpc->shouldReceive('call')->once()->with('jobs.Push', \Mockery::on(
            static function (PushRequest $request) use (&$sentHeaders): bool {
                $sentHeaders = [];
                foreach ($request->getJob()->getHeaders() as $name => $value) {
                    $sentHeaders[$name] = \iterator_to_array($value->getValue());
                }

                return true;
            },
        ));

        $pipeline->send(new PreparedTask('bar', 'foo=bar', headers: ['foo' => ['bar', 'baz'], 'empty' => []]));

        Assert::same($sentHeaders, ['foo' => ['bar', 'baz']]);
    }

    public function testSendTakesOptionsFromGettersWithoutToArray(): void
    {
        $task = \Mockery::mock(PreparedTaskInterface::class);
        $task->shouldReceive('getName')->andReturn('bar');
        $task->shouldReceive('getPayload')->andReturn('foo=bar');
        $task->shouldReceive('getHeaders')->andReturn([]);
        $task->shouldReceive('getPriority')->andReturn(7);
        $task->shouldReceive('getDelay')->andReturn(3);
        $task->shouldReceive('getAutoAck')->andReturn(true);

        $pipeline = new Pipeline(
            'foo',
            $rpc = \Mockery::mock(RPCInterface::class),
            $uuid = \Mockery::mock(UuidFactoryInterface::class),
        );
        $uuid->shouldReceive('uuid4')->andReturn($id = Uuid::uuid4());

        $sentOptions = null;
        $rpc->shouldReceive('call')->once()->with('jobs.Push', \Mockery::on(
            static function (PushRequest $request) use (&$sentOptions): bool {
                $sentOptions = $request->getJob()->getOptions();

                return true;
            },
        ));

        $queuedTask = $pipeline->send($task);

        Assert::same($queuedTask->getId(), (string) $id);
        Assert::instanceOf($sentOptions, DTOOptions::class);
        Assert::same($sentOptions->getPriority(), 7);
        Assert::same($sentOptions->getDelay(), 3);
        Assert::true($sentOptions->getAutoAck());
        Assert::same($sentOptions->getPipeline(), 'foo');
    }
}
