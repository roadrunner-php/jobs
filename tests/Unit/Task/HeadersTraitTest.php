<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;

#[Test]
final class HeadersTraitTest
{
    public function testGetsHeaders(): void
    {
        $task = $this->getTask($headers = ['foo' => ['bar']]);

        Assert::same($task->getHeaders(), $headers);
    }

    public function testHasHeader(): void
    {
        $task = $this->getTask(['foo' => ['bar'], 'bar' => []]);

        Assert::true($task->hasHeader('foo'));
        Assert::false($task->hasHeader('bar'));
        Assert::false($task->hasHeader('baz'));
    }

    public function testGetsHeaderLine(): void
    {
        $task = $this->getTask(['foo' => ['bar', 'baz', 'baf']]);

        Assert::same($task->getHeaderLine('foo'), 'bar,baz,baf');
        Assert::same($task->getHeaderLine('bar'), '');
    }

    public function testGetsHeader(): void
    {
        $task = $this->getTask(['foo' => ['bar'], 'bar' => []]);

        Assert::same($task->getHeader('foo'), ['bar']);
        Assert::same($task->getHeader('bar'), []);
        Assert::same($task->getHeader('baz'), []);
    }

    public function getTask(array $headers = ['foo' => ['bar']]): PreparedTask
    {
        return new PreparedTask(
            'foo',
            'foo=bar',
            headers: $headers,
        );
    }
}
