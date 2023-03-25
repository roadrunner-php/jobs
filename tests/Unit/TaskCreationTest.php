<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsFactory;
use Spiral\RoadRunner\Jobs\Queue;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\QueueInterface;

class TaskCreationTestCase extends TestCase
{
    public function testTaskCreation(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'foo=bar');

        $this->assertSame($expected, $task->getName());
    }

    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     * @return QueueInterface
     */
    protected function queue(array $mapping = [], string $name = 'queue', ?Driver $driver = null): QueueInterface
    {
        return new Queue($name, $this->rpc($mapping), $driver !== null ? OptionsFactory::create($driver) : null);
    }

    public function testTaskCreationWithPayload(): void
    {
        $expected = 'payload';

        $task = $this->queue()
            ->create('task', $expected);

        $this->assertSame($expected, $task->getPayload());
    }

    public function testTaskCreationWithDefaultOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'foo=bar');

        $this->assertSame($expected, $task->getName());
        $this->assertSame(0, $task->getDelay());
        $this->assertSame(0, $task->getPriority());
        $this->assertFalse($task->getAutoAck());
    }

    public function testTaskCreationWithOverriddenDefaultOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $queue = $this->queue()->withDefaultOptions(new Options(10, 100, true));

        $task = $queue->create($expected, 'foo=bar');

        $this->assertSame($expected, $task->getName());
        $this->assertSame(10, $task->getDelay());
        $this->assertSame(100, $task->getPriority());
        $this->assertTrue($task->getAutoAck());
    }

    public function testTaskCreationWithOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'bar', new Options(10, 100, true));

        $this->assertSame($expected, $task->getName());
        $this->assertSame(10, $task->getDelay());
        $this->assertSame(100, $task->getPriority());
        $this->assertTrue($task->getAutoAck());
    }

    public function testTaskCreationPassedOptionsHighPriority(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $queue = $this->queue()->withDefaultOptions(new Options(10, 100, true));

        $task = $queue->create($expected, 'bar', new Options(10, 150, true));

        $this->assertSame($expected, $task->getName());
        $this->assertSame(10, $task->getDelay());
        $this->assertSame(150, $task->getPriority());
        $this->assertTrue($task->getAutoAck());
    }

    public function testTaskCreationOtherRealizationOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this
            ->queue([], 'queue', Queue\Driver::Kafka)
            ->create($expected,'bar', new KafkaOptions('kafka-topic', 15, 30, false));
        $options = $task->getOptions();

        $this->assertInstanceOf(KafkaOptions::class, $options);
        $this->assertSame('kafka-topic', $options->getTopic());
        $this->assertSame(15, $task->getDelay());
        $this->assertSame(30, $task->getPriority());
        $this->assertFalse($task->getAutoAck());
    }
}
