<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Jobs\DTO\V1\Options as DTOOptions;
use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Queue\Pipeline;
use Spiral\RoadRunner\Jobs\QueueInterface;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;

final class PipelineTest extends TestCase
{
    /** @dataProvider taskToProtoDataProvider */
    public function testTaskToProto(OptionsInterface $options, DTOOptions $expected): void
    {
        $pipeline = new Pipeline(
            $this->createMock(QueueInterface::class),
            $this->createMock(RPCInterface::class)
        );

        $method = new \ReflectionMethod($pipeline, 'taskToProto');
        $method->setAccessible(true);

        $task = new PreparedTask('foo', '', $options);
        $job = $method->invoke($pipeline, $task, $task);

        $this->assertEquals($expected, $job->getOptions());
    }

    public function taskToProtoDataProvider(): \Traversable
    {
        yield [new Options(5, 10), new DTOOptions([
            'priority' => 10,
            'pipeline' => '',
            'delay' => 5,
            'auto_ack' => false,
            'topic' => '',
            'metadata' => ''
        ])];

        yield [new KafkaOptions('some', 10, 5, true, 'other', 5, 7), new DTOOptions([
            'priority' => 5,
            'pipeline' => '',
            'delay' => 10,
            'auto_ack' => true,
            'topic' => 'some',
            'metadata' => 'other',
            'offset' => 5,
            'partition' => 7
        ])];
    }
}
