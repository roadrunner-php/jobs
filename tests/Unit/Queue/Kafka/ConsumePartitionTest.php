<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;

use function json_encode;

final class ConsumePartitionTest extends TestCase
{
    public function testConstructor(): void
    {
        $consumePartition = new ConsumePartition(
            $topic = 'my-topic',
            $partition = 1,
            $offset = new ConsumerOffset(OffsetType::AtStart, 123)
        );

        $this->assertInstanceOf(ConsumePartition::class, $consumePartition);
        $this->assertSame($topic, $consumePartition->topic);
        $this->assertSame($partition, $consumePartition->partition);
        $this->assertSame($offset, $consumePartition->offset);
    }

    public function testSerialization(): void
    {
        $string = json_encode(
            new ConsumePartition(
                'my-topic', 1, new ConsumerOffset(OffsetType::AtStart, 123)
            ),
        );

        $this->assertSame(
            '{"topic":"my-topic","partition":1,"offset":{"type":"AtStart","value":123}}',
            $string,
        );
    }
}
