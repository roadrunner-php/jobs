<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\GroupOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\SASL;

/**
 * The DTO to create the Kafka driver.
 */
final class KafkaCreateInfo extends CreateInfo
{
    public const AUTO_CREATE_TOPICS_ENABLE_VALUE = false;

    /**
     * @param non-empty-string $name
     * @param SASL|null $sasl SASL authentication options to use for all connections. Depending on the auth type, plain
     * or aws_msk_plain sections might be removed.
     * @param non-empty-string[] $brokers Kafka brokers. If there is no port specified, 9092 will be used as default
     * @param positive-int $priority Queue default priority
     * @param bool $autoCreateTopics Auto create topic for the consumer/producer
     * @param ProducerOptions|null $producerOptions Kafka producer options
     * @param ConsumerOptions|null $consumerOptions Kafka Consumer options. Needed to consume messages from the Kafka
     * cluster.
     * @param GroupOptions|null $groupOptions sets the consumer group for the client to join and consume in. This
     * option is required if using any other group options.
     */
    public function __construct(
        string $name,
        public readonly array $brokers = ['127.0.0.1:9092'],
        public readonly ?SASL $sasl = null,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly bool $autoCreateTopics = self::AUTO_CREATE_TOPICS_ENABLE_VALUE,
        public readonly ?ProducerOptions $producerOptions = null,
        public readonly ?ConsumerOptions $consumerOptions = null,
        public readonly ?GroupOptions $groupOptions = null,
    ) {
        parent::__construct(Driver::Kafka, $name, $priority);
    }

    public function toArray(): array
    {
        $info = [
            'brokers' => $this->brokers,
            'autoCreateTopics' => $this->autoCreateTopics,
        ];

        if ($this->sasl !== null) {
            $info['sasl'] = $this->sasl;
        }

        if ($this->producerOptions !== null) {
            $info['producer_options'] = $this->producerOptions;
        }

        if ($this->consumerOptions !== null) {
            $info['consumer_options'] = $this->consumerOptions;
        }

        if ($this->groupOptions !== null) {
            $info['group_options'] = $this->groupOptions;
        }

        return \array_merge(parent::toArray(), $info);
    }
}
