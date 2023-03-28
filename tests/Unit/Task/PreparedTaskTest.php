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
        $task = new PreparedTask('foo', [], $options);

        $this->assertEquals($expected, $task->getOptions());
    }

    public function testWithOptions(): void
    {
        $task = new PreparedTask('foo', []);

        $this->assertSame(5, $task->withOptions(new Options(5))->getDelay());
        $this->assertSame('changed', $task->withOptions(new KafkaOptions('changed'))->getOptions()->getTopic());
    }

    public function testCreatingTaskWithHeaders(): void
    {
        $task = new PreparedTask('foo', [], null, ['foo' => ['bar']]);

        $this->assertSame(['foo' => ['bar']], $task->getHeaders());
    }

    public function testCreatingTaskWithoutHeaders(): void
    {
        $task = new PreparedTask('foo', []);

        $this->assertSame([], $task->getHeaders());
    }

    public function optionsDataProvider(): \Traversable
    {
        yield [new Options(), null];
        yield [(new Options())->withDelay(5), (new Options())->withDelay(5)];
        yield [new KafkaOptions('default'), new KafkaOptions('default')];
        yield [(new KafkaOptions('default'))->withDelay(10), (new KafkaOptions('default'))->withDelay(10)];
    }
}
