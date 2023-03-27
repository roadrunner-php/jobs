<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerGroupOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;
use Spiral\RoadRunner\Jobs\Queue\Kafka\SASL;

/**
 * The DTO to create the Kafka driver.
 */
final class KafkaCreateInfo extends CreateInfo
{
    public const AUTO_CREATE_TOPICS_ENABLE_DEFAULT_VALUE = false;

    /**
     * @param non-empty-string $name
     * @param SASL|null $sasl SASL authentication options to use for all connections. Depending on the auth type, plain
     * or aws_msk_plain sections might be removed.
     * @param non-empty-string[] $brokers Kafka brokers. If there is no port specified, 9092 will be used as default
     * @param positive-int $priority Queue default priority
     * @param bool $autoCreateTopicsEnable Auto create topic for the consumer/producer
     * @param ProducerOptions|null $producerOptions Kafka producer options.
     * @param ConsumerOptions|null $consumerOptions Kafka Consumer options. Needed to consume messages from the Kafka
     * cluster.
     * @param ConsumerGroupOptions|null $groupOptions sets the consumer group for the client to join and consume in. This
     * option is required if using any other group options.
     */
    public function __construct(
        string $name,
        public readonly array $brokers = ['127.0.0.1:9092'],
        public readonly ?SASL $sasl = null,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly bool $autoCreateTopicsEnable = self::AUTO_CREATE_TOPICS_ENABLE_DEFAULT_VALUE,
        public readonly ?ProducerOptions $producerOptions = null,
        public readonly ?ConsumerOptions $consumerOptions = null,
        public readonly ?ConsumerGroupOptions $groupOptions = null,
    ) {
        parent::__construct(Driver::Kafka, $name, $priority);

        \assert($this->brokers !== [], 'Precondition [brokers !== []] failed');
    }

    public function toArray(): array
    {
        $info = [
            'brokers' => $this->brokers,
            'auto_create_topics_enable' => $this->autoCreateTopicsEnable,
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
