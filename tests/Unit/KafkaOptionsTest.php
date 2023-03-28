<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;

use function json_encode;

final class KafkaOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $this->assertEquals('my-topic', $options->getTopic());
        $this->assertEquals(100, $options->getDelay());
        $this->assertEquals(10, $options->getPriority());
        $this->assertTrue($options->getAutoAck());
        $this->assertEquals('metadata', $options->getMetadata());
        $this->assertEquals(50, $options->getOffset());
        $this->assertEquals(1, $options->getPartition());
    }

    public function testFrom(): void
    {
        $parentOptions = new KafkaOptions('parent-topic', 50, 5, false);
        $options = KafkaOptions::from($parentOptions);

        $this->assertEquals('parent-topic', $options->getTopic());
        $this->assertEquals(50, $options->getDelay());
        $this->assertEquals(5, $options->getPriority());
        $this->assertFalse($options->getAutoAck());
        $this->assertEquals(KafkaOptions::DEFAULT_METADATA, $options->getMetadata());
        $this->assertEquals(KafkaOptions::DEFAULT_OFFSET, $options->getOffset());
        $this->assertEquals(KafkaOptions::DEFAULT_PARTITION, $options->getPartition());

        $childOptions = new KafkaOptions('child-topic', 100, 10, true, 'metadata', 50, 1);
        $options = KafkaOptions::from($childOptions);

        $this->assertEquals('child-topic', $options->getTopic());
        $this->assertEquals(100, $options->getDelay());
        $this->assertEquals(10, $options->getPriority());
        $this->assertTrue($options->getAutoAck());
        $this->assertEquals('metadata', $options->getMetadata());
        $this->assertEquals(50, $options->getOffset());
        $this->assertEquals(1, $options->getPartition());
    }

    public function testMerge(): void
    {
        $parentOptions = new KafkaOptions('parent-topic', 50, 5, false, 'metadata-1', 10, 2);
        $childOptions = new KafkaOptions('child-topic', 100, 10, true, 'metadata-2', 20, 3);

        $options = $parentOptions->merge($childOptions);

        $this->assertEquals('child-topic', $options->getTopic());
        $this->assertEquals(100, $options->getDelay());
        $this->assertEquals(10, $options->getPriority());
        $this->assertTrue($options->getAutoAck());
        $this->assertEquals('metadata-2', $options->getMetadata());
        $this->assertEquals(20, $options->getOffset());
        $this->assertEquals(3, $options->getPartition());
    }

    public function testWithTopic(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withTopic('new-topic');

        $this->assertEquals('new-topic', $newOptions->getTopic());
        $this->assertNotEquals($options->getTopic(), $newOptions->getTopic());
    }

    public function testWithMetadata(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withMetadata('new-metadata');

        $this->assertEquals('new-metadata', $newOptions->getMetadata());
        $this->assertNotEquals($options->getMetadata(), $newOptions->getMetadata());
    }

    public function testWithOffset(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withOffset(100);

        $this->assertEquals(100, $newOptions->getOffset());
        $this->assertNotEquals($options->getOffset(), $newOptions->getOffset());
    }

    public function testWithPartition(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $newOptions = $options->withPartition(2);

        $this->assertEquals(2, $newOptions->getPartition());
        $this->assertNotEquals($options->getPartition(), $newOptions->getPartition());
    }

    public function testToArray(): void
    {
        $options = new KafkaOptions('my-topic', 100, 10, true, 'metadata', 50, 1);

        $this->assertSame(
            <<<'JOSN'
{
    "priority": 10,
    "delay": 100,
    "auto_ack": true,
    "topic": "my-topic",
    "metadata": "metadata",
    "offset": 50,
    "partition": 1
}
JOSN
            ,
            json_encode($options, JSON_PRETTY_PRINT),
        );
    }
}
