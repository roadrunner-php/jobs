<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;

final class ConsumePartitionTest extends TestCase
{
    public function testConstructor(): void
    {
        $topic = 'my-topic';
        $partition = 1;
        $offset = new ConsumerOffset(OffsetType::AtStart, 123);

        $consumePartition = new ConsumePartition($topic, $partition, $offset);

        $this->assertInstanceOf(ConsumePartition::class, $consumePartition);
        $this->assertSame($topic, $consumePartition->topic);
        $this->assertSame($partition, $consumePartition->partition);
        $this->assertSame($offset, $consumePartition->offset);
    }

    public function testConstructorWithoutOffset(): void
    {
        $topic = 'my-topic';
        $partition = 1;
        $consumePartition = new ConsumePartition($topic, $partition);

        $this->assertInstanceOf(ConsumePartition::class, $consumePartition);
        $this->assertSame($topic, $consumePartition->topic);
        $this->assertSame($partition, $consumePartition->partition);
        $this->assertNull($consumePartition->offset);
    }
}