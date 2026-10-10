<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsFactory;
use Spiral\RoadRunner\Jobs\Queue;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\QueueInterface;
use Testo\Assert;
use Testo\Test;

#[Test]
final class TaskCreationTest extends BaseTestCase
{
    public function testTaskCreation(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'foo=bar');

        Assert::same($task->getName(), $expected);
    }

    public function testTaskCreationWithPayload(): void
    {
        $expected = 'payload';

        $task = $this->queue()
            ->create('task', $expected);

        Assert::same($task->getPayload(), $expected);
    }

    public function testTaskCreationWithDefaultOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'foo=bar');

        Assert::same($task->getName(), $expected);
        Assert::same($task->getDelay(), 0);
        Assert::same($task->getPriority(), 0);
        Assert::false($task->getAutoAck());
    }

    public function testTaskCreationWithOverriddenDefaultOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $queue = $this->queue()->withDefaultOptions(new Options(10, 100, true));

        $task = $queue->create($expected, 'foo=bar');

        Assert::same($task->getName(), $expected);
        Assert::same($task->getDelay(), 10);
        Assert::same($task->getPriority(), 100);
        Assert::true($task->getAutoAck());
    }

    public function testTaskCreationWithOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()->create($expected, 'bar', new Options(10, 100, true));

        Assert::same($task->getName(), $expected);
        Assert::same($task->getDelay(), 10);
        Assert::same($task->getPriority(), 100);
        Assert::true($task->getAutoAck());
    }

    public function testTaskCreationPassedOptionsHighPriority(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $queue = $this->queue()->withDefaultOptions(new Options(10, 100, true));

        $task = $queue->create($expected, 'bar', new Options(10, 150, true));

        Assert::same($task->getName(), $expected);
        Assert::same($task->getDelay(), 10);
        Assert::same($task->getPriority(), 150);
        Assert::true($task->getAutoAck());
    }

    public function testTaskCreationOtherRealizationOptions(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this
            ->queue([], 'queue', Driver::Kafka)
            ->create($expected, 'bar', new KafkaOptions('kafka-topic', 15, 30, false));
        $options = $task->getOptions();

        Assert::instanceOf($options, KafkaOptions::class);
        Assert::same($options->getTopic(), 'kafka-topic');
        Assert::same($task->getDelay(), 15);
        Assert::same($task->getPriority(), 30);
        Assert::false($task->getAutoAck());
    }

    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     */
    protected function queue(array $mapping = [], string $name = 'queue', ?Driver $driver = null): QueueInterface
    {
        return new Queue($name, $this->rpc($mapping), $driver !== null ? OptionsFactory::create($driver) : null);
    }
}
