<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumePartition;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\GroupOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;
use Spiral\RoadRunner\Jobs\Queue\KafkaCreateInfo;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\SASL;

final class KafkaCreateInfoTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $createInfo = new KafkaCreateInfo('test');

        $this->assertSame(['127.0.0.1:9092'], $createInfo->brokers);
        $this->assertNull($createInfo->sasl);
        $this->assertFalse($createInfo->autoCreateTopics);
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
            'brokers' => ['127.0.0.1:9092'],
            'autoCreateTopics' => false,
        ], $createInfo->toArray());
    }

    public function testCustomValues(): void
    {
        $sasl = SASL::basic('user', 'password');
        $producerOptions = new ProducerOptions();
        $consumerOptions = new ConsumerOptions(['topic1', 'topic2'], consumePartitions: [
            new ConsumePartition('topic1', 100, new ConsumerOffset(OffsetType::AtEnd, 3)),
            new ConsumePartition('topic2', 200, new ConsumerOffset(OffsetType::Relative, 5)),
        ]);
        $groupOptions = new GroupOptions('group');

        $createInfo = new KafkaCreateInfo(
            'test',
            ['127.0.0.1:9092', 'localhost:9092'],
            $sasl,
            1,
            true,
            $producerOptions,
            $consumerOptions,
            $groupOptions
        );

        $expected = [
            'name' => 'test',
            'driver' => 'kafka',
            'priority' => 1,
            'brokers' => ['127.0.0.1:9092', 'localhost:9092'],
            'autoCreateTopics' => true,
            'sasl' => [
                'mechanism' => 'plain',
                'username' => 'user',
                'password' => 'password',
            ],
            'producer_options' => [
                'disable_idempotent' => false,
                'required_acks' => 'AllISRAck',
                'max_message_bytes' => 1000012,
                'request_timeout' => '10s',
                'delivery_timeout' => '100s',
                'transaction_timeout' => '40s',
            ],
            'consumer_options' => [
                'topics' => ['topic1', 'topic2'],
                'consume_regexp' => false,
                'max_fetch_message_size' => 50000,
                'min_fetch_message_size' => 1,
                'consumer_offset' => [
                    'type' => 'AtStart',
                    'value' => 1,
                ],
                'consume_partitions' => [
                    'topic1' => [
                        100 => [
                            'type' => 'AtEnd',
                            'value' => 3,
                        ],
                    ],
                    'topic2' => [
                        200 => [
                            'type' => 'Relative',
                            'value' => 5,
                        ],
                    ],
                ],
            ],
            'group_options' => [
                'group_id' => 'group',
                'block_rebalance_on_poll' => false,
            ],
        ];

        $this->assertSame(\json_encode($expected), \json_encode($createInfo));

        var_dump(\json_encode($createInfo->toArray(), JSON_PRETTY_PRINT));
    }
}