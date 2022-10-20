<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\SQSCreateInfo;

final class SQSCreateInfoTest extends TestCase
{
    public function testCreateWithTags(): void
    {
        $info = new SQSCreateInfo(
            'foo',
            SQSCreateInfo::PRIORITY_DEFAULT_VALUE,
            SQSCreateInfo::PREFETCH_DEFAULT_VALUE,
            SQSCreateInfo::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
            SQSCreateInfo::WAIT_TIME_SECONDS_DEFAULT_VALUE,
            SQSCreateInfo::QUEUE_DEFAULT_VALUE,
            SQSCreateInfo::ATTRIBUTES_DEFAULT_VALUE,
            ['foo' => 'bar']
        );

        $this->assertSame([
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time_seconds' => 0,
            'queue' => 'default',
            'tags' => ['foo' => 'bar']
        ], $info->toArray());
    }

    public function testCreateWithAttributes(): void
    {
        $info = new SQSCreateInfo(
            'foo',
            SQSCreateInfo::PRIORITY_DEFAULT_VALUE,
            SQSCreateInfo::PREFETCH_DEFAULT_VALUE,
            SQSCreateInfo::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
            SQSCreateInfo::WAIT_TIME_SECONDS_DEFAULT_VALUE,
            SQSCreateInfo::QUEUE_DEFAULT_VALUE,
            ['foo' => 'bar']
        );

        $this->assertSame([
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time_seconds' => 0,
            'queue' => 'default',
            'attributes' => ['foo' => 'bar']
        ], $info->toArray());
    }

    public function testCreateWithTagsAndAttributes(): void
    {
        $info = new SQSCreateInfo(
            'foo',
            SQSCreateInfo::PRIORITY_DEFAULT_VALUE,
            SQSCreateInfo::PREFETCH_DEFAULT_VALUE,
            SQSCreateInfo::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
            SQSCreateInfo::WAIT_TIME_SECONDS_DEFAULT_VALUE,
            SQSCreateInfo::QUEUE_DEFAULT_VALUE,
            ['foo' => 'bar'],
            ['baz' => 'some']
        );

        $this->assertSame([
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time_seconds' => 0,
            'queue' => 'default',
            'attributes' => ['foo' => 'bar'],
            'tags' => ['baz' => 'some']
        ], $info->toArray());
    }

    public function testCreateWithoutTagsAndAttributes(): void
    {
        $info = new SQSCreateInfo('foo');

        $this->assertSame([
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time_seconds' => 0,
            'queue' => 'default'
        ], $info->toArray());
    }
}
