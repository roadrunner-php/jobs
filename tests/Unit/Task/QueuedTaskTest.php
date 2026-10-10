<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Spiral\RoadRunner\Jobs\Task\QueuedTask;
use Testo\Assert;
use Testo\Test;

#[Test]
final class QueuedTaskTest
{
    public function testGetters(): void
    {
        $id = '12345';
        $queue = 'default';
        $name = 'TestTask';
        $payload = 'foo=bar';
        $headers = ['foo' => ['bar', 'baz']];

        $task = new QueuedTask($id, $queue, $name, $payload, $headers);

        Assert::equals($task->getId(), $id);
        Assert::equals($task->getPipeline(), $queue);
        Assert::equals($task->getName(), $name);
        Assert::equals($task->getPayload(), $payload);
        Assert::equals($task->getHeaders(), $headers);
    }
}
