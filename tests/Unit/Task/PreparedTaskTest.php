<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Testo\Test;
use Testo\Data\DataProvider;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;

#[Test]
final class PreparedTaskTest
{
    public static function optionsDataProvider(): \Traversable
    {
        yield [new Options(), null];
        yield [(new Options())->withDelay(5), (new Options())->withDelay(5)];
        yield [new KafkaOptions('default'), new KafkaOptions('default')];
        yield [(new KafkaOptions('default'))->withDelay(10), (new KafkaOptions('default'))->withDelay(10)];
    }

    #[DataProvider('optionsDataProvider')]
    public function testGetOptions(OptionsInterface $expected, ?OptionsInterface $options = null): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar', options: $options);

        Assert::equals($task->getOptions(), $expected);
    }

    public function testWithOptions(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        Assert::same($task->withOptions(new Options(5))->getDelay(), 5);
        Assert::same($task->withOptions(new KafkaOptions('changed'))->getOptions()->getTopic(), 'changed');
    }

    public function testDelay(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        Assert::equals($task->getDelay(), 0);

        $task = $task->withDelay(100);
        Assert::equals($task->getDelay(), 100);
    }

    public function testPriority(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        Assert::equals($task->getPriority(), 0);

        $task = $task->withPriority(100);
        Assert::equals($task->getPriority(), 100);
    }

    public function testCreatingTaskWithHeaders(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar', options: null, headers: ['foo' => ['bar']]);

        Assert::same($task->getHeaders(), ['foo' => ['bar']]);
    }

    public function testCreatingTaskWithoutHeaders(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        Assert::same($task->getHeaders(), []);
    }

    public function testAutoAck(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');
        Assert::false($task->getAutoAck());

        $task = $task->withAutoAck(true);
        Assert::true($task->getAutoAck());
    }
}
