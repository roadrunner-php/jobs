<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Task;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;

final class HeadersTraitTest extends TestCase
{
    public function testGetsHeaders(): void
    {
        $task = $this->getTask($headers = ['foo' => ['bar']]);

        $this->assertSame($headers, $task->getHeaders());
    }

    public function testHasHeader(): void
    {
        $task = $this->getTask(['foo' => ['bar'], 'bar' => []]);

        $this->assertTrue($task->hasHeader('foo'));
        $this->assertFalse($task->hasHeader('bar'));
        $this->assertFalse($task->hasHeader('baz'));
    }

    public function testGetsHeaderLine(): void
    {
        $task = $this->getTask(['foo' => ['bar', 'baz', 'baf']]);

        $this->assertSame('bar,baz,baf', $task->getHeaderLine('foo'));
        $this->assertSame('', $task->getHeaderLine('bar'));
    }

    public function testGetsHeader(): void
    {
        $task = $this->getTask(['foo' => ['bar'], 'bar' => []]);

        $this->assertSame(['bar'], $task->getHeader('foo'));
        $this->assertSame([], $task->getHeader('bar'));
        $this->assertSame([], $task->getHeader('baz'));
    }

    public function getTask(array $headers = ['foo' => ['bar']]): PreparedTask
    {
        return new PreparedTask(
            'foo',
            'foo=bar',
            headers: $headers
        );
    }
}
