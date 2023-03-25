<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;

final class PreparedTaskTest extends TestCase
{
    /** @dataProvider optionsDataProvider */
    public function testGetOptions(OptionsInterface $expected, ?OptionsInterface $options = null): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar', options: $options);

        $this->assertEquals($expected, $task->getOptions());
    }

    public function testWithOptions(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        $this->assertSame(5, $task->withOptions(new Options(5))->getDelay());
        $this->assertSame('changed', $task->withOptions(new KafkaOptions('changed'))->getOptions()->getTopic());
    }

    public function testDelay(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        $this->assertEquals(0, $task->getDelay());

        $task = $task->withDelay(100);
        $this->assertEquals(100, $task->getDelay());
    }

    public function testPriority(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        $this->assertEquals(0, $task->getPriority());

        $task = $task->withPriority(100);
        $this->assertEquals(100, $task->getPriority());
    }

    public function testCreatingTaskWithHeaders(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar', options: null, headers: ['foo' => ['bar']]);

        $this->assertSame(['foo' => ['bar']], $task->getHeaders());
    }

    public function testCreatingTaskWithoutHeaders(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');

        $this->assertSame([], $task->getHeaders());
    }

    public function testAutoAck(): void
    {
        $task = new PreparedTask(name: 'foo', payload: 'bar');
        $this->assertFalse($task->getAutoAck());

        $task = $task->withAutoAck(true);
        $this->assertTrue($task->getAutoAck());
    }

    public function optionsDataProvider(): \Traversable
    {
        yield [new Options(), null];
        yield [(new Options())->withDelay(5),(new Options())->withDelay(5)];
        yield [new KafkaOptions('default'), new KafkaOptions('default')];
        yield [(new KafkaOptions('default'))->withDelay(10), (new KafkaOptions('default'))->withDelay(10)];
    }
}
