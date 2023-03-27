<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;
use PHPUnit\Framework\TestCase;

final class ConsumerOptionsTest extends TestCase
{
    public function testConstructor(): void
    {
        $topics = ['my-topic'];
        $consumeRegexp = true;
        $maxFetchMessageSize = 100_000;
        $minFetchMessageSize = 10;
        $consumePartitions = [
            new ConsumePartition('my-topic', 1, new ConsumerOffset(OffsetType::AtStart, 0)),
            new ConsumePartition('my-topic1', 2, new ConsumerOffset(OffsetType::AtEnd, 0)),
        ];
        $consumerOffset = new ConsumerOffset(OffsetType::AtEnd, 1);

        $consumerOptions = new ConsumerOptions(
            $topics,
            $consumeRegexp,
            $maxFetchMessageSize,
            $minFetchMessageSize,
            $consumePartitions,
            $consumerOffset
        );

        $this->assertInstanceOf(ConsumerOptions::class, $consumerOptions);
        $this->assertEquals($topics, $consumerOptions->topics);
        $this->assertEquals($consumeRegexp, $consumerOptions->consumeRegexp);
        $this->assertEquals($maxFetchMessageSize, $consumerOptions->maxFetchMessageSize);
        $this->assertEquals($minFetchMessageSize, $consumerOptions->minFetchMessageSize);
        $this->assertEquals($consumePartitions, $consumerOptions->consumePartitions);
        $this->assertEquals($consumerOffset, $consumerOptions->consumerOffset);
    }

    public function testConstructorWithDefaultValues(): void
    {
        $topics = ['my-topic'];

        $consumerOptions = new ConsumerOptions($topics);

        $this->assertInstanceOf(ConsumerOptions::class, $consumerOptions);
        $this->assertEquals($topics, $consumerOptions->topics);
        $this->assertFalse($consumerOptions->consumeRegexp);
        $this->assertEquals(
            ConsumerOptions::CONSUMER_MAX_FETCH_MESSAGE_SIZE_DEFAULT_VALUE,
            $consumerOptions->maxFetchMessageSize,
        );
        $this->assertEquals(
            ConsumerOptions::CONSUMER_MIN_FETCH_MESSAGE_SIZE_DEFAULT_VALUE,
            $consumerOptions->minFetchMessageSize,
        );
        $this->assertEmpty($consumerOptions->consumePartitions);
        $this->assertInstanceOf(ConsumerOffset::class, $consumerOptions->consumerOffset);
        $this->assertEquals(OffsetType::AtStart, $consumerOptions->consumerOffset->type);
        $this->assertEquals(1, $consumerOptions->consumerOffset->value);
    }

    public function testJsonSerialize(): void
    {
        $topics = ['my-topic'];
        $consumeRegexp = true;
        $maxFetchMessageSize = 100_000;
        $minFetchMessageSize = 10;
        $consumePartitions = [
            new ConsumePartition('my-topic', 1, new ConsumerOffset(OffsetType::AtStart, 0)),
            new ConsumePartition('my-topic', 2, new ConsumerOffset(OffsetType::AtEnd, 0)),
        ];
        $consumerOffset = new ConsumerOffset(OffsetType::AtEnd, 1);

        $consumerOptions = new ConsumerOptions(
            $topics,
            $consumeRegexp,
            $maxFetchMessageSize,
            $minFetchMessageSize,
            $consumePartitions,
            $consumerOffset
        );

        $this->assertEquals(
            <<<'JOSN'
{
    "topics": [
        "my-topic"
    ],
    "consume_regexp": true,
    "max_fetch_message_size": 100000,
    "min_fetch_message_size": 10,
    "consumer_offset": {
        "type": "AtEnd",
        "value": 1
    },
    "consume_partitions": {
        "my-topic": {
            "1": {
                "type": "AtStart",
                "value": 0
            },
            "2": {
                "type": "AtEnd",
                "value": 0
            }
        }
    }
}
JOSN
            ,
            \json_encode($consumerOptions, JSON_PRETTY_PRINT),
        );
    }

    public function testJsonSerializeWithoutConsumePartitions(): void
    {
        $this->assertEquals(
            <<<'JOSN'
{
    "topics": [
        "my-topic"
    ],
    "consume_regexp": false,
    "max_fetch_message_size": 50000,
    "min_fetch_message_size": 1,
    "consumer_offset": {
        "type": "AtStart",
        "value": 1
    }
}
JOSN
            ,
            \json_encode(new ConsumerOptions(['my-topic']), JSON_PRETTY_PRINT),
        );
    }
}