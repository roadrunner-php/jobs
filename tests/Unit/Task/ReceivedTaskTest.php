<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Jobs\Task\Type;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

final class ReceivedTaskTest extends TestCase
{
    private MockObject|WorkerInterface $worker;

    public function testGetsPipeline(): void
    {
        $task = $this->createTask(pipeline: 'custom');
        $this->assertSame('custom', $task->getPipeline());
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
            $this->worker, $id, $driver, $pipeline, $name, $queue, $payload, $headers
        );
    }



    public function testGetsQueue(): void
    {
        $task = $this->createTask(queue: 'broker-queue-name');

        $this->assertEquals('broker-queue-name', $task->getQueue());
    }

    public function testGetsDriver(): void
    {
        $task = $this->createTask(driver: Driver::Kafka);
        $this->assertEquals(Driver::Kafka, $task->getDriver());

        $task = $this->createTask(driver: Driver::Unknown);
        $this->assertEquals(Driver::Unknown, $task->getDriver());
    }

    public function testComplete(): void
    {
        $task = $this->createTask();

        $this->assertFalse($task->isCompleted());
        $this->assertFalse($task->isFails());
        $this->assertFalse($task->isSuccessful());

        $this->worker->expects($this->once())
            ->method('respond')
            ->with(
                $this->callback(function (Payload $payload) {
                    $this->assertEquals('{"type":0,"data":[]}', $payload->body);

                    return true;
                }),
            );

        $task->complete();

        $this->assertTrue($task->isCompleted());
        $this->assertTrue($task->isSuccessful());
        $this->assertFalse($task->isFails());
    }


    public function provideFailData(): Generator
    {
        yield 'default' => ['Some error message', false, null, []];
        yield 'requeue' => ['Some error message', true, null, []];
        yield 'delay' => ['Some error message', false, 10, []];
        yield 'headers' => ['Some error message', false, null, ['foo' => 'bar']];
    }

    /**
     * @dataProvider provideFailData
     */
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

        $this->assertFalse($task->isCompleted());
        $this->assertFalse($task->isFails());
        $this->assertFalse($task->isSuccessful());

        $this->worker->expects($this->once())
            ->method('respond')
            ->with(
                $this->callback(function (Payload $payload) use ($delay, $requeue, $error, $headers) {
                    $result = [
                        'type' => Type::ERROR,
                        'data' => [
                            'message' => $error,
                            'requeue' => $requeue,
                            'delay_seconds' => (int)$delay,
                        ],
                    ];

                    if (!empty($headers)) {
                        $result['data']['headers'] = $headers;
                    }

                    $this->assertEquals(
                        \json_encode($result),
                        $payload->body,
                    );

                    return true;
                }),
            );

        $task->fail(error: $error, requeue: $requeue);

        $this->assertTrue($task->isFails());
        $this->assertFalse($task->isSuccessful());
        $this->assertTrue($task->isCompleted());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->worker = $this->createMock(WorkerInterface::class);
    }
}
