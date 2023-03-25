<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task\Factory;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Serializer\JsonSerializer;
use Spiral\RoadRunner\Jobs\Serializer\SerializerInterface;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactory;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;

final class ReceivedTaskFactoryTest extends TestCase
{
    /**
     * @dataProvider payloadsDataProvider
     */
    public function testCreate(Payload $payload, string $expectedTaskClass, Driver $expectedDriver): void
    {
        $factory = new ReceivedTaskFactory($this->createMock(WorkerInterface::class));

        $task = $factory->create($payload);

        $this->assertInstanceOf($expectedTaskClass, $task);
        $this->assertSame($expectedDriver, $task->getDriver());

        $this->assertSame('job-id', $task->getId());
        $this->assertSame('job-pipeline', $task->getPipeline());
        $this->assertSame('job-queue', $task->getQueue());
        $this->assertSame('job-name', $task->getName());
    }

    public function testKafkaReceivedTaskShouldReceiveCorrectParams(): void
    {
        $factory = new ReceivedTaskFactory($this->createMock(WorkerInterface::class));

        $task = $factory->create(
            new Payload(
                \json_encode(['foo' => 'bar']),
                \json_encode([
                    'id' => 'job-id',
                    'queue' => 'job-queue',
                    'pipeline' => 'job-pipeline',
                    'job' => 'job-name',
                    'partition' => 3,
                    'offset' => 5,
                    'headers' => ['foo' => 'bar'],
                    'driver' => Driver::Kafka->value,
                ])
            ),
        );

        $this->assertInstanceOf(KafkaReceivedTask::class, $task);

        $this->assertSame('job-id', $task->getId());
        $this->assertSame('job-pipeline', $task->getPipeline());
        $this->assertSame('job-queue', $task->getQueue());
        $this->assertSame('job-name', $task->getName());
        $this->assertSame(3, $task->getPartition());
        $this->assertSame(5, $task->getOffset());
    }

    public function payloadsDataProvider(): \Traversable
    {
        foreach (Driver::cases() as $driver) {
            if ($driver === Driver::Kafka) {
                continue;
            }

            yield $driver->value => [
                new Payload(
                    \json_encode(['foo' => 'bar']),
                    \json_encode([
                        'id' => 'job-id',
                        'queue' => 'job-queue',
                        'pipeline' => 'job-pipeline',
                        'job' => 'job-name',
                        'headers' => ['foo' => 'bar'],
                        'driver' => $driver->value,
                    ])
                ),
                ReceivedTask::class,
                $driver,
            ];
        }

        // without driver, for backward compatibility
        yield 'without driver' => [
            new Payload(
                \json_encode(['foo' => 'bar']),
                \json_encode([
                    'id' => 'job-id',
                    'queue' => 'job-queue',
                    'pipeline' => 'job-pipeline',
                    'job' => 'job-name',
                    'headers' => ['foo' => 'bar'],
                ])
            ),
            ReceivedTask::class,
            Driver::Unknown,
        ];


        yield 'kafka' => [
            new Payload(
                \json_encode(['foo' => 'bar']),
                \json_encode([
                    'id' => 'job-id',
                    'queue' => 'job-queue',
                    'pipeline' => 'job-pipeline',
                    'job' => 'job-name',
                    'topic' => 'foo',
                    'partition' => 3,
                    'offset' => 5,
                    'headers' => ['foo' => 'bar'],
                    'driver' => Driver::Kafka->value,
                ])
            ),
            KafkaReceivedTask::class,
            Driver::Kafka,
        ];
    }
}
