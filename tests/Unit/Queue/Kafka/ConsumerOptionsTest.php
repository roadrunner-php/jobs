<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Testo\Test;
use Testo\Assert;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;

#[Test]
final class ConsumerOptionsTest
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
            $consumerOffset,
        );

        Assert::instanceOf($consumerOptions, ConsumerOptions::class);
        Assert::equals($consumerOptions->topics, $topics);
        Assert::equals($consumerOptions->consumeRegexp, $consumeRegexp);
        Assert::equals($consumerOptions->maxFetchMessageSize, $maxFetchMessageSize);
        Assert::equals($consumerOptions->minFetchMessageSize, $minFetchMessageSize);
        Assert::equals($consumerOptions->consumePartitions, $consumePartitions);
        Assert::equals($consumerOptions->consumerOffset, $consumerOffset);
    }

    public function testConstructorWithDefaultValues(): void
    {
        $topics = ['my-topic'];

        $consumerOptions = new ConsumerOptions($topics);

        Assert::instanceOf($consumerOptions, ConsumerOptions::class);
        Assert::equals($consumerOptions->topics, $topics);
        Assert::false($consumerOptions->consumeRegexp);
        Assert::equals($consumerOptions->maxFetchMessageSize, ConsumerOptions::CONSUMER_MAX_FETCH_MESSAGE_SIZE_DEFAULT_VALUE);
        Assert::equals($consumerOptions->minFetchMessageSize, ConsumerOptions::CONSUMER_MIN_FETCH_MESSAGE_SIZE_DEFAULT_VALUE);
        Assert::blank($consumerOptions->consumePartitions);
        Assert::instanceOf($consumerOptions->consumerOffset, ConsumerOffset::class);
        Assert::equals($consumerOptions->consumerOffset->type, OffsetType::AtStart);
        Assert::equals($consumerOptions->consumerOffset->value, 1);
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
            $consumerOffset,
        );

        Assert::equals(\json_encode($consumerOptions, JSON_PRETTY_PRINT), <<<'JOSN'
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
JOSN);
    }

    public function testJsonSerializeWithoutConsumePartitions(): void
    {
        Assert::equals(\json_encode(new ConsumerOptions(['my-topic']), JSON_PRETTY_PRINT), <<<'JOSN'
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
JOSN);
    }
}
