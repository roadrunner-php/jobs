<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;

#[Test]
final class KafkaOptionsTest
{
    public function testConstructor(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        Assert::equals($options->getTopic(), 'my-topic');
        Assert::equals($options->getDelay(), 100);
        Assert::equals($options->getPriority(), 10);
        Assert::true($options->getAutoAck());
        Assert::equals($options->getMetadata(), 'metadata');
        Assert::equals($options->getOffset(), 50);
        Assert::equals($options->getPartition(), 1);
    }

    public function testFrom(): void
    {
        $parentOptions = new KafkaOptions('parent-topic', 50, 5, false);
        $options = KafkaOptions::from($parentOptions);

        Assert::equals($options->getTopic(), 'parent-topic');
        Assert::equals($options->getDelay(), 50);
        Assert::equals($options->getPriority(), 5);
        Assert::false($options->getAutoAck());
        Assert::equals($options->getMetadata(), KafkaOptions::DEFAULT_METADATA);
        Assert::equals($options->getOffset(), KafkaOptions::DEFAULT_OFFSET);
        Assert::equals($options->getPartition(), KafkaOptions::DEFAULT_PARTITION);

        $childOptions = new KafkaOptions('child-topic', 100, 10, true, 'metadata', 50, 1);
        $options = KafkaOptions::from($childOptions);

        Assert::equals($options->getTopic(), 'child-topic');
        Assert::equals($options->getDelay(), 100);
        Assert::equals($options->getPriority(), 10);
        Assert::true($options->getAutoAck());
        Assert::equals($options->getMetadata(), 'metadata');
        Assert::equals($options->getOffset(), 50);
        Assert::equals($options->getPartition(), 1);
    }

    public function testFromNonKafkaOptions(): void
    {
        $options = KafkaOptions::from(new Options(delay: 15, priority: 3, autoAck: true));

        Assert::same($options->getTopic(), 'default');
        Assert::same($options->getDelay(), 15);
        Assert::same($options->getPriority(), 3);
        Assert::true($options->getAutoAck());
        Assert::same($options->getMetadata(), KafkaOptions::DEFAULT_METADATA);
        Assert::same($options->getOffset(), KafkaOptions::DEFAULT_OFFSET);
        Assert::same($options->getPartition(), KafkaOptions::DEFAULT_PARTITION);
    }

    public function testMerge(): void
    {
        $parentOptions = new KafkaOptions('parent-topic', 50, 5, false, 'metadata-1', 10, 2);
        $childOptions = new KafkaOptions('child-topic', 100, 10, true, 'metadata-2', 20, 3);

        $options = $parentOptions->merge($childOptions);

        Assert::equals($options->getTopic(), 'child-topic');
        Assert::equals($options->getDelay(), 100);
        Assert::equals($options->getPriority(), 10);
        Assert::true($options->getAutoAck());
        Assert::equals($options->getMetadata(), 'metadata-2');
        Assert::equals($options->getOffset(), 20);
        Assert::equals($options->getPartition(), 3);
    }

    public function testWithTopic(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withTopic('new-topic');

        Assert::equals($newOptions->getTopic(), 'new-topic');
        Assert::notEquals($newOptions->getTopic(), $options->getTopic());
    }

    public function testWithMetadata(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withMetadata('new-metadata');

        Assert::equals($newOptions->getMetadata(), 'new-metadata');
        Assert::notEquals($newOptions->getMetadata(), $options->getMetadata());
    }

    public function testWithOffset(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withOffset(100);

        Assert::equals($newOptions->getOffset(), 100);
        Assert::notEquals($newOptions->getOffset(), $options->getOffset());
    }

    public function testWithPartition(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withPartition(2);

        Assert::equals($newOptions->getPartition(), 2);
        Assert::notEquals($newOptions->getPartition(), $options->getPartition());
    }

    public function testToArray(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        Assert::same(\json_encode($options, JSON_PRETTY_PRINT), <<<'JOSN'
{
    "priority": 10,
    "delay": 100,
    "auto_ack": true,
    "topic": "my-topic",
    "metadata": "metadata",
    "offset": 50,
    "partition": 1
}
JOSN);
    }
}
