<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\PubSubCreateInfo;

#[Test]
final class PubSubCreateInfoTest
{
    public function testCreatePubSubCreateInfo(): void
    {
        $pubSubCreateInfo = new PubSubCreateInfo(
            name: 'test_name',
            projectId: 'test_project_id',
            topic: 'test_topic',
            priority: 3,
            deadLetterTopic: 'test_dead_letter_topic',
            maxDeliveryAttempts: 15,
        );

        Assert::same($pubSubCreateInfo->driver, Driver::PubSub);
        Assert::same($pubSubCreateInfo->name, 'test_name');
        Assert::same($pubSubCreateInfo->projectId, 'test_project_id');
        Assert::same($pubSubCreateInfo->topic, 'test_topic');
        Assert::same($pubSubCreateInfo->priority, 3);
        Assert::same($pubSubCreateInfo->deadLetterTopic, 'test_dead_letter_topic');
        Assert::same($pubSubCreateInfo->maxDeliveryAttempts, 15);
    }

    public function testCreatePubSubCreateInfoOnlyRequiredData(): void
    {
        $pubSubCreateInfo = new PubSubCreateInfo(
            name: 'test_name',
            projectId: 'test_project_id',
            topic: 'test_topic',
        );

        Assert::same($pubSubCreateInfo->driver, Driver::PubSub);
        Assert::same($pubSubCreateInfo->name, 'test_name');
        Assert::same($pubSubCreateInfo->projectId, 'test_project_id');
        Assert::same($pubSubCreateInfo->topic, 'test_topic');
        Assert::same($pubSubCreateInfo->priority, 10);
        Assert::null($pubSubCreateInfo->deadLetterTopic);
        Assert::same($pubSubCreateInfo->maxDeliveryAttempts, 10);
    }

    public function testToArray(): void
    {
        $pubSubCreateInfo = new PubSubCreateInfo(
            name: 'test_name',
            projectId: 'test_project_id',
            topic: 'test_topic',
            priority: 3,
            deadLetterTopic: 'test_dead_letter_topic',
            maxDeliveryAttempts: 15,
        );

        $expectedArray = [
            'name' => 'test_name',
            'driver' => Driver::PubSub->value,
            'priority' => 3,
            'project_id' => 'test_project_id',
            'topic' => 'test_topic',
            'dead_letter_topic' => 'test_dead_letter_topic',
            'max_delivery_attempts' => 15,
        ];

        Assert::same($pubSubCreateInfo->toArray(), $expectedArray);
    }

    public function testToArrayOnlyRequiredData(): void
    {
        $pubSubCreateInfo = new PubSubCreateInfo(
            name: 'test_name',
            projectId: 'test_project_id',
            topic: 'test_topic',
        );

        $expectedArray = [
            'name' => 'test_name',
            'driver' => Driver::PubSub->value,
            'priority' => 10,
            'project_id' => 'test_project_id',
            'topic' => 'test_topic',
        ];

        Assert::same($pubSubCreateInfo->toArray(), $expectedArray);
    }
}
