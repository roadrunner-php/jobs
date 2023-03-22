<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Task\QueuedTask;

final class QueuedTaskTest extends TestCase
{
    public function testGetters()
    {
        $id = '12345';
        $queue = 'default';
        $name = 'TestTask';
        $payload = 'foo=bar';
        $headers = ['foo' => ['bar', 'baz']];

        $task = new QueuedTask($id, $queue, $name, $payload, $headers);

        $this->assertEquals($id, $task->getId());
        $this->assertEquals($queue, $task->getQueue());
        $this->assertEquals($name, $task->getName());
        $this->assertEquals($payload, $task->getPayload());
        $this->assertEquals($headers, $task->getHeaders());
    }
}