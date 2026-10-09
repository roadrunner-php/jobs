<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Testo\Test;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\BeforeTest;
use Mockery\MockInterface;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Jobs\Task\Type;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

#[Test]
final class ReceivedTaskTest
{
    private MockInterface|WorkerInterface $worker;

    public static function provideFailData(): \Generator
    {
        yield 'default' => ['Some error message', false, null, []];
        yield 'requeue' => ['Some error message', true, null, []];
        yield 'delay' => ['Some error message', false, 10, []];
        yield 'headers' => ['Some error message', false, null, ['foo' => 'bar']];
    }

    public function testGetsPipeline(): void
    {
        $task = $this->createTask(pipeline: 'custom');
        Assert::same($task->getPipeline(), 'custom');
    }

    public function createTask(
        Driver $driver = Driver::Kafka,
        string $id = '12345',
        string $pipeline = 'default',
        string $queue = 'default',
        string $name = 'TestTask',
        string $payload = 'foo=bar',
        array $headers = [],
    ): ReceivedTaskInterface {
        return new ReceivedTask(
            $this->worker,
            $id,
            $driver,
            $pipeline,
            $name,
            $queue,
            $payload,
            $headers,
        );
    }

    public function testGetsQueue(): void
    {
        $task = $this->createTask(queue: 'broker-queue-name');

        Assert::equals($task->getQueue(), 'broker-queue-name');
    }

    public function testGetsDriver(): void
    {
        $task = $this->createTask(driver: Driver::Kafka);
        Assert::equals($task->getDriver(), Driver::Kafka);

        $task = $this->createTask(driver: Driver::Unknown);
        Assert::equals($task->getDriver(), Driver::Unknown);
    }

    public function testComplete(): void
    {
        $task = $this->createTask();

        Assert::false($task->isCompleted());
        Assert::false($task->isFails());
        Assert::false($task->isSuccessful());

        $this->worker->shouldReceive('respond')->once()->with(\Mockery::on(function (Payload $payload) {
            Assert::equals($payload->body, '{"type":0,"data":[]}');

            return true;
        }), \Mockery::andAnyOtherArgs());

        $task->complete();

        Assert::true($task->isCompleted());
        Assert::true($task->isSuccessful());
        Assert::false($task->isFails());
    }

    public function testAck(): void
    {
        $task = $this->createTask();

        Assert::false($task->isCompleted());
        Assert::false($task->isFails());
        Assert::false($task->isSuccessful());

        $this->worker->shouldReceive('respond')->once()->with(\Mockery::on(function (Payload $payload) {
            Assert::equals($payload->body, '{"type":2,"data":[]}');

            return true;
        }), \Mockery::andAnyOtherArgs());

        $task->ack();

        Assert::true($task->isCompleted());
        Assert::true($task->isSuccessful());
        Assert::false($task->isFails());
    }

    #[DataProvider('provideFailData')]
    public function testNack(string $error, bool $redelivery, int|null $delay): void
    {
        $task = $this->createTask();

        if ($delay !== null) {
            $task = $task->withDelay($delay);
        }

        Assert::false($task->isCompleted());
        Assert::false($task->isFails());
        Assert::false($task->isSuccessful());

        $this->worker->shouldReceive('respond')->once()->with(\Mockery::on(function (Payload $payload) use ($delay, $redelivery, $error) {
            $result = [
                'type' => Type::NACK,
                'data' => [
                    'message' => $error,
                    'requeue' => $redelivery,
                    'delay_seconds' => (int) $delay,
                ],
            ];

            Assert::equals($payload->body, \json_encode($result));

            return true;
        }), \Mockery::andAnyOtherArgs());

        $task->nack(message: $error, redelivery: $redelivery);

        Assert::true($task->isFails());
        Assert::false($task->isSuccessful());
        Assert::true($task->isCompleted());
    }

    #[DataProvider('provideFailData')]
    public function testRequeue(string $error, bool $requeue, int|null $delay, array $headers): void
    {
        $task = $this->createTask();

        if ($delay !== null) {
            $task = $task->withDelay($delay);
        }

        foreach ($headers as $key => $value) {
            $task = $task->withHeader($key, $value);
            $headers[$key] = [$value];
        }

        Assert::false($task->isCompleted());
        Assert::false($task->isFails());
        Assert::false($task->isSuccessful());

        $this->worker->shouldReceive('respond')->once()->with(\Mockery::on(function (Payload $payload) use ($delay, $error, $headers) {
            $result = [
                'type' => Type::REQUEUE,
                'data' => [
                    'message' => $error,
                    'delay_seconds' => (int) $delay,
                ],
            ];

            if (!empty($headers)) {
                $result['data']['headers'] = $headers;
            }

            Assert::equals($payload->body, \json_encode($result));

            return true;
        }), \Mockery::andAnyOtherArgs());

        $task->requeue(message: $error);

        Assert::true($task->isFails());
        Assert::false($task->isSuccessful());
        Assert::true($task->isCompleted());
    }

    #[DataProvider('provideFailData')]
    public function testFail($error, bool $requeue, int|null $delay, array $headers): void
    {
        $task = $this->createTask();

        if ($delay !== null) {
            $task = $task->withDelay($delay);
        }

        foreach ($headers as $key => $value) {
            $task = $task->withHeader($key, $value);
            $headers[$key] = [$value];
        }

        Assert::false($task->isCompleted());
        Assert::false($task->isFails());
        Assert::false($task->isSuccessful());

        $this->worker->shouldReceive('respond')->once()->with(\Mockery::on(function (Payload $payload) use ($delay, $requeue, $error, $headers) {
            $result = [
                'type' => Type::ERROR,
                'data' => [
                    'message' => $error,
                    'requeue' => $requeue,
                    'delay_seconds' => (int) $delay,
                ],
            ];

            if (!empty($headers)) {
                $result['data']['headers'] = $headers;
            }

            Assert::equals($payload->body, \json_encode($result));

            return true;
        }), \Mockery::andAnyOtherArgs());

        $task->fail(error: $error, requeue: $requeue);

        Assert::true($task->isFails());
        Assert::false($task->isSuccessful());
        Assert::true($task->isCompleted());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->worker = \Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing();
    }
}
