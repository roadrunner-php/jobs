<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerGroupOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\SASL;
use Spiral\RoadRunner\Jobs\Queue\KafkaCreateInfo;

use function json_encode;

final class KafkaCreateInfoTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $createInfo = new KafkaCreateInfo('test');

        $this->assertFalse($createInfo->autoCreateTopicsEnable);
        $this->assertNull($createInfo->producerOptions);
        $this->assertNull($createInfo->consumerOptions);
        $this->assertNull($createInfo->groupOptions);
        $this->assertSame(Driver::Kafka, $createInfo->getDriver());
        $this->assertSame('test', $createInfo->getName());
        $this->assertSame(10, $createInfo->priority);
        $this->assertSame([
            'name' => 'test',
            'driver' => 'kafka',
            'priority' => 10,
            'auto_create_topics_enable' => false,
        ], $createInfo->toArray());
    }

    public function testCustomValues(): void
    {
        $producerOptions = new ProducerOptions();
        $consumerOptions = new ConsumerOptions(['topic1', 'topic2'], consumePartitions: [
            new ConsumePartition('topic1', 100, new ConsumerOffset(OffsetType::AtEnd, 3)),
            new ConsumePartition('topic2', 200, new ConsumerOffset(OffsetType::Relative, 5)),
        ]);
        $groupOptions = new ConsumerGroupOptions('group');

        $createInfo = new KafkaCreateInfo(
            'test',
            1,
            true,
            $producerOptions,
            $consumerOptions,
            $groupOptions
        );

        $this->assertSame(
            <<<'JSON'
{
    "name": "test",
    "driver": "kafka",
    "priority": 1,
    "auto_create_topics_enable": true,
    "producer_options": {
        "disable_idempotent": false,
        "max_message_bytes": 1000012,
        "required_acks": "AllISRAck"
    },
    "consumer_options": {
        "topics": [
            "topic1",
            "topic2"
        ],
        "consume_regexp": false,
        "max_fetch_message_size": 50000,
        "min_fetch_message_size": 1,
        "consumer_offset": {
            "type": "AtStart",
            "value": 1
        },
        "consume_partitions": {
            "topic1": {
                "100": {
                    "type": "AtEnd",
                    "value": 3
                }
            },
            "topic2": {
                "200": {
                    "type": "Relative",
                    "value": 5
                }
            }
        }
    },
    "group_options": {
        "group_id": "group",
        "block_rebalance_on_poll": false
    }
}
JSON
            ,
            json_encode($createInfo, JSON_PRETTY_PRINT),
        );
    }
}
