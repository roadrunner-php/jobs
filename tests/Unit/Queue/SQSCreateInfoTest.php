<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\SQSCreateInfo;

final class SQSCreateInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $sqsCreateInfo = new SQSCreateInfo(
            'testName',
            1,
            20,
            30,
            40,
            'customQueue',
            ['key' => 'value'],
            ['tagKey' => 'tagValue']
        );

        $this->assertEquals(Driver::SQS, $sqsCreateInfo->driver);
        $this->assertEquals('testName', $sqsCreateInfo->name);
        $this->assertEquals(1, $sqsCreateInfo->priority);
        $this->assertEquals(20, $sqsCreateInfo->prefetch);
        $this->assertEquals(30, $sqsCreateInfo->visibilityTimeout);
        $this->assertEquals(40, $sqsCreateInfo->waitTimeSeconds);
        $this->assertEquals('customQueue', $sqsCreateInfo->queue);
        $this->assertEquals(['key' => 'value'], $sqsCreateInfo->attributes);
        $this->assertEquals(['tagKey' => 'tagValue'], $sqsCreateInfo->tags);
    }

    public function testToArray(): void
    {
        $sqsCreateInfo = new SQSCreateInfo(
            'testName',
            1,
            20,
            30,
            40,
            'customQueue',
            ['key' => 'value'],
            ['tagKey' => 'tagValue']
        );

        $result = $sqsCreateInfo->toArray();
        $expected = [
            'driver' => Driver::SQS,
            'name' => 'testName',
            'priority' => 1,
            'prefetch' => 20,
            'visibility_timeout' => 30,
            'wait_time_seconds' => 40,
            'queue' => 'customQueue',
            'attributes' => ['key' => 'value'],
            'tags' => ['tagKey' => 'tagValue'],
        ];

        $this->assertEquals($expected, $result);
    }


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
            'tags' => ['foo' => 'bar'],
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
            'attributes' => ['foo' => 'bar'],
        ], $info->toArray());
    }

    public function testToArrayWithDefaults()
    {
        $sqsCreateInfo = new SQSCreateInfo('testName');

        $result = $sqsCreateInfo->toArray();
        $expected = [
            'driver'             => Driver::SQS,
            'name'               => 'testName',
            'priority'           => SQSCreateInfo::PRIORITY_DEFAULT_VALUE,
            'prefetch'           => SQSCreateInfo::PREFETCH_DEFAULT_VALUE,
            'visibility_timeout' => SQSCreateInfo::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
            'wait_time_seconds'  => SQSCreateInfo::WAIT_TIME_SECONDS_DEFAULT_VALUE,
            'queue'              => SQSCreateInfo::QUEUE_DEFAULT_VALUE,
        ];

        $this->assertEquals($expected, $result);
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
            'tags' => ['baz' => 'some'],
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
            'queue' => 'default',
        ], $info->toArray());
    }
}
