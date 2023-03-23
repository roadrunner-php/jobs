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
    public function testCreate(Payload $payload, string $expectedTaskClass): void
    {
        $factory = new ReceivedTaskFactory($this->createMock(WorkerInterface::class));

        $task = $factory->create($payload);

        $this->assertInstanceOf($expectedTaskClass, $task);
    }

    public function testKafkaReceivedTaskShouldReceiveCorrectParams(): void
    {
        $factory = new ReceivedTaskFactory($this->createMock(WorkerInterface::class));

        $task = $factory->create(new Payload(json_encode(['foo' => 'bar']), \json_encode([
            'id' => 'job-id',
            'pipeline' => 'job-pipeline',
            'job' => 'job-name',
            'topic' => 'job-topic',
            'partition' => 3,
            'offset' => 5,
            'headers' => ['foo' => 'bar'],
            'driver' => Driver::Kafka->value
        ])));

        $this->assertSame('job-topic', $task->getTopic());
        $this->assertSame(3, $task->getPartition());
        $this->assertSame(5, $task->getOffset());
    }

    public function payloadsDataProvider(): \Traversable
    {
        // without driver, for backward compatibility
        yield [new Payload(json_encode(['foo' => 'bar']), \json_encode([
            'id' => 'job-id',
            'pipeline' => 'job-pipeline',
            'job' => 'job-name',
            'headers' => ['foo' => 'bar'],
        ])), ReceivedTask::class];

        yield [new Payload(json_encode(['foo' => 'bar']), \json_encode([
            'id' => 'job-id',
            'pipeline' => 'job-pipeline',
            'job' => 'job-name',
            'headers' => ['foo' => 'bar'],
            'driver' => Driver::Memory->value
        ])), ReceivedTask::class];

        yield [new Payload(json_encode(['foo' => 'bar']), \json_encode([
            'id' => 'job-id',
            'pipeline' => 'job-pipeline',
            'job' => 'job-name',
            'topic' => 'foo',
            'partition' => 3,
            'offset' => 5,
            'headers' => ['foo' => 'bar'],
            'driver' => Driver::Kafka->value
        ])), KafkaReceivedTask::class];
    }
}
