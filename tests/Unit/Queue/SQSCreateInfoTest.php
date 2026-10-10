<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\SQSCreateInfo;
use Testo\Assert;
use Testo\Test;

#[Test]
final class SQSCreateInfoTest
{
    public function testConstructor(): void
    {
        $sqsCreateInfo = new SQSCreateInfo(
            name: 'testName',
            priority: 1,
            prefetch: 20,
            visibilityTimeout: 30,
            waitTimeSeconds: 40,
            queue: 'customQueue',
            attributes: ['key' => 'value'],
            tags: ['tagKey' => 'tagValue'],
            messageGroupId: 'customMessageGroupId',
            skipQueueDeclaration: true,
        );

        Assert::equals($sqsCreateInfo->driver, Driver::SQS);
        Assert::equals($sqsCreateInfo->name, 'testName');
        Assert::equals($sqsCreateInfo->priority, 1);
        Assert::equals($sqsCreateInfo->prefetch, 20);
        Assert::equals($sqsCreateInfo->visibilityTimeout, 30);
        Assert::equals($sqsCreateInfo->waitTimeSeconds, 40);
        Assert::equals($sqsCreateInfo->queue, 'customQueue');
        Assert::equals($sqsCreateInfo->attributes, ['key' => 'value']);
        Assert::equals($sqsCreateInfo->tags, ['tagKey' => 'tagValue']);
        Assert::equals($sqsCreateInfo->messageGroupId, 'customMessageGroupId');
        Assert::true($sqsCreateInfo->skipQueueDeclaration);
    }

    public function testToArray(): void
    {
        $sqsCreateInfo = new SQSCreateInfo(
            name: 'testName',
            priority: 1,
            prefetch: 20,
            visibilityTimeout: 30,
            waitTimeSeconds: 40,
            queue: 'customQueue',
            attributes: ['key' => 'value'],
            tags: ['tagKey' => 'tagValue'],
        );

        $result = $sqsCreateInfo->toArray();
        $expected = [
            'driver' => Driver::SQS->value,
            'name' => 'testName',
            'priority' => 1,
            'prefetch' => 20,
            'visibility_timeout' => 30,
            'wait_time' => 40,
            'queue' => 'customQueue',
            'attributes' => ['key' => 'value'],
            'tags' => ['tagKey' => 'tagValue'],
            'skip_queue_declaration' => false,
        ];

        Assert::equals($result, $expected);
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
            ['foo' => 'bar'],
        );

        Assert::equals($info->toArray(), [
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time' => 0,
            'queue' => 'default',
            'tags' => ['foo' => 'bar'],
            'skip_queue_declaration' => false,
        ]);
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
            ['foo' => 'bar'],
        );

        Assert::equals($info->toArray(), [
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time' => 0,
            'queue' => 'default',
            'attributes' => ['foo' => 'bar'],
            'skip_queue_declaration' => false,
        ]);
    }

    public function testToArrayWithDefaults(): void
    {
        $sqsCreateInfo = new SQSCreateInfo('testName');

        $result = $sqsCreateInfo->toArray();
        $expected = [
            'driver' => Driver::SQS->value,
            'name' => 'testName',
            'priority' => SQSCreateInfo::PRIORITY_DEFAULT_VALUE,
            'prefetch' => SQSCreateInfo::PREFETCH_DEFAULT_VALUE,
            'visibility_timeout' => SQSCreateInfo::VISIBILITY_TIMEOUT_DEFAULT_VALUE,
            'wait_time' => SQSCreateInfo::WAIT_TIME_SECONDS_DEFAULT_VALUE,
            'queue' => SQSCreateInfo::QUEUE_DEFAULT_VALUE,
            'skip_queue_declaration' => false,
        ];

        Assert::equals($result, $expected);
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
            ['baz' => 'some'],
        );

        Assert::equals($info->toArray(), [
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time' => 0,
            'queue' => 'default',
            'attributes' => ['foo' => 'bar'],
            'tags' => ['baz' => 'some'],
            'skip_queue_declaration' => false,
        ]);
    }

    public function testCreateWithoutTagsAndAttributes(): void
    {
        $info = new SQSCreateInfo('foo');

        Assert::same($info->toArray(), [
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time' => 0,
            'queue' => 'default',
            'skip_queue_declaration' => false,
        ]);
    }

    public function testCreateWithMessageGroupId(): void
    {
        $info = new SQSCreateInfo(name: 'foo', messageGroupId: 'bar');

        Assert::same($info->toArray(), [
            'name' => 'foo',
            'driver' => 'sqs',
            'priority' => 10,
            'prefetch' => 10,
            'visibility_timeout' => 0,
            'wait_time' => 0,
            'queue' => 'default',
            'skip_queue_declaration' => false,
            'message_group_id' => 'bar',
        ]);
    }
}
