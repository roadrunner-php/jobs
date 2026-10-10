<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task\Factory;

use Spiral\RoadRunner\Jobs\Exception\ReceivedTaskException;
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactory;
use Spiral\RoadRunner\Jobs\Task\KafkaReceivedTask;
use Spiral\RoadRunner\Jobs\Task\ReceivedTask;
use Spiral\RoadRunner\Payload;
use Spiral\RoadRunner\WorkerInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Expect;
use Testo\Test;

#[Test]
final class ReceivedTaskFactoryTest
{
    public static function payloadsDataProvider(): \Traversable
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
                    ]),
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
                ]),
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
                ]),
            ),
            KafkaReceivedTask::class,
            Driver::Kafka,
        ];
    }

    public function testEmptyHeader(): void
    {
        Expect::exception(ReceivedTaskException::class)->withMessageContaining('Task payload does not have a valid header.');

        $factory = new ReceivedTaskFactory(\Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing());
        $factory->create(new Payload(null));
    }

    public function testMalformedHeader(): void
    {
        Expect::exception(SerializationException::class);

        $factory = new ReceivedTaskFactory(\Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing());
        $factory->create(new Payload(null, '{"id":'));
    }

    public function testEmptyBody(): void
    {
        $factory = new ReceivedTaskFactory(\Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing());
        $task = $factory->create(new Payload(
            null,
            \json_encode([
                'id' => 'job-id',
                'queue' => 'job-queue',
                'driver' => 'memory',
                'pipeline' => 'job-pipeline',
                'job' => 'job-name',
                'headers' => ['foo' => 'bar'],
            ]),
        ));

        Assert::same($task->getPayload(), '');
    }

    #[DataProvider('payloadsDataProvider')]
    public function testCreate(Payload $payload, string $expectedTaskClass, Driver $expectedDriver): void
    {
        $factory = new ReceivedTaskFactory(\Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing());

        $task = $factory->create($payload);

        Assert::instanceOf($task, $expectedTaskClass);
        Assert::same($task->getDriver(), $expectedDriver);

        Assert::same($task->getId(), 'job-id');
        Assert::same($task->getPipeline(), 'job-pipeline');
        Assert::same($task->getQueue(), 'job-queue');
        Assert::same($task->getName(), 'job-name');
    }

    public function testKafkaReceivedTaskShouldReceiveCorrectParams(): void
    {
        $factory = new ReceivedTaskFactory(\Mockery::mock(WorkerInterface::class)->shouldIgnoreMissing());

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
                ]),
            ),
        );

        Assert::instanceOf($task, KafkaReceivedTask::class);

        Assert::same($task->getId(), 'job-id');
        Assert::same($task->getPipeline(), 'job-pipeline');
        Assert::same($task->getQueue(), 'job-queue');
        Assert::same($task->getName(), 'job-name');
        Assert::same($task->getPartition(), 3);
        Assert::same($task->getOffset(), 5);
    }
}
