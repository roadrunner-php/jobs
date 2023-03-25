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

        $expected = [
            'topics' => $topics,
            'consume_regexp' => $consumeRegexp,
            'max_fetch_message_size' => $maxFetchMessageSize,
            'min_fetch_message_size' => $minFetchMessageSize,
            'consumer_offset' => $consumerOffset,
            'consume_partitions' => [
                'my-topic' => [
                    1 => $consumePartitions[0]->offset,
                    2 => $consumePartitions[1]->offset,
                ],
            ],
        ];

        $this->assertEquals($expected, $consumerOptions->jsonSerialize());
    }

    public function testJsonSerializeWithoutConsumePartitions(): void
    {
        $topics = ['my-topic'];

        $consumerOptions = new ConsumerOptions($topics);

        $expected = [
            'topics' => $topics,
            'consume_regexp' => ConsumerOptions::CONSUMER_REGEXP_DEFAULT_VALUE,
            'max_fetch_message_size' => ConsumerOptions::CONSUMER_MAX_FETCH_MESSAGE_SIZE_DEFAULT_VALUE,
            'min_fetch_message_size' => ConsumerOptions::CONSUMER_MIN_FETCH_MESSAGE_SIZE_DEFAULT_VALUE,
            'consumer_offset' => $consumerOptions->consumerOffset,
        ];

        $this->assertEquals($expected, $consumerOptions->jsonSerialize());
    }
}