<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use Spiral\RoadRunner\Jobs\Task\PreparedTask;
use Testo\Assert;
use Testo\Test;

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

    public function testWithAddedHeaderAppendsValues(): void
    {
        $task = $this->getTask(['foo' => ['bar']]);

        $withString = $task->withAddedHeader('foo', 'baz');
        $withList = $withString->withAddedHeader('foo', ['qux', 'quux']);
        $withNew = $task->withAddedHeader('new', 'value');

        Assert::same($task->getHeader('foo'), ['bar']);
        Assert::same($withString->getHeader('foo'), ['bar', 'baz']);
        Assert::same($withList->getHeader('foo'), ['bar', 'baz', 'qux', 'quux']);
        Assert::same($withNew->getHeaders(), ['foo' => ['bar'], 'new' => ['value']]);
    }

    public function testWithoutHeaderRemovesHeader(): void
    {
        $task = $this->getTask(['foo' => ['bar'], 'baz' => ['qux']]);

        $without = $task->withoutHeader('foo');

        Assert::notSame($without, $task);
        Assert::same($without->getHeaders(), ['baz' => ['qux']]);
        Assert::same($task->getHeaders(), ['foo' => ['bar'], 'baz' => ['qux']]);
    }

    public function testWithoutMissingHeaderKeepsInstance(): void
    {
        $task = $this->getTask(['foo' => ['bar']]);

        Assert::same($task->withoutHeader('missing'), $task);
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
