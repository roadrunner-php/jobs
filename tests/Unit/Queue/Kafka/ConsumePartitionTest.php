<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;

#[Test]
final class ConsumePartitionTest
{
    public function testConstructor(): void
    {
        $consumePartition = new ConsumePartition(
            $topic = 'my-topic',
            $partition = 1,
            $offset = new ConsumerOffset(OffsetType::AtStart, 123),
        );

        Assert::instanceOf($consumePartition, ConsumePartition::class);
        Assert::same($consumePartition->topic, $topic);
        Assert::same($consumePartition->partition, $partition);
        Assert::same($consumePartition->offset, $offset);
    }

    public function testSerialization(): void
    {
        $string = \json_encode(
            new ConsumePartition(
                'my-topic',
                1,
                new ConsumerOffset(OffsetType::AtStart, 123),
            ),
        );

        Assert::same($string, '{"topic":"my-topic","partition":1,"offset":{"type":"AtStart","value":123}}');
    }
}
