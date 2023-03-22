<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\Kafka\CompressionCodec;
use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\RequiredAcks;

/**
 * The DTO to create the Kafka driver.
 */
final class KafkaCreateInfo extends CreateInfo
{
    /**
     * @var string
     */
    public const GROUP_ID_DEFAULT_VALUE = '';

    /**
     * @var positive-int
     */
    public const MAX_OPEN_REQUESTS_DEFAULT_VALUE = 5;

    /**
     * @var non-empty-string
     */
    public const CLIENT_ID_DEFAULT_VALUE = 'roadrunner';

    /**
     * @var non-empty-string
     */
    public const KAFKA_VERSION_DEFAULT_VALUE = '3.2.0.0';

    /**
     * @var positive-int
     */
    public const REPLICATION_FACTOR_DEFAULT_VALUE = 1;

    /**
     * @var positive-int
     */
    public const MAX_MESSAGE_BYTES_DEFAULT_VALUE = 1000000;

    /**
     * @var positive-int
     */
    public const TIMEOUT_DEFAULT_VALUE = 10;

    /**
     * @var positive-int
     */
    public const COMPRESSION_LEVEL_DEFAULT_VALUE = 100;

    /**
     * @var bool
     */
    public const IDEMPOTENT_DEFAULT_VALUE = false;

    /**
     * @var positive-int
     */
    public const HEARTBEAT_INTERVAL_DEFAULT_VALUE = 3;

    /**
     * @var positive-int
     */
    public const SESSION_TIMEOUT_DEFAULT_VALUE = 10;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $topic
     * @param positive-int $priority
     * @param array<positive-int, value-of<PartitionOffset>|int>|null $partitionsOffsets
     * @param positive-int $maxOpenRequests
     * @param non-empty-string $clientId
     * @param non-empty-string $kafkaVersion
     * @param positive-int $replicationFactor
     * @param array<positive-int, array<positive-int>>|null $replicaAssignment
     * @param array<non-empty-string, mixed>|null $configEntries
     * @param positive-int $maxMessageBytes
     * @param RequiredAcks|value-of<RequiredAcks> $requiredAcks
     * @param positive-int $timeout
     * @param CompressionCodec|value-of<CompressionCodec> $compressionCodec
     * @param positive-int $compressionLevel
     * @param positive-int $heartbeatInterval
     * @param positive-int $sessionTimeout
     */
    public function __construct(
        string $name,
        /** @see https://kafka.apache.org/intro#intro_concepts_and_terms */
        public readonly string $topic,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        public readonly string $groupId = self::GROUP_ID_DEFAULT_VALUE,
        public readonly ?array $partitionsOffsets = null,
        /** @see https://kafka.apache.org/28/documentation.html#producerconfigs_max.in.flight.requests.per.connection */
        public readonly int $maxOpenRequests = self::MAX_OPEN_REQUESTS_DEFAULT_VALUE,
        public readonly string $clientId = self::CLIENT_ID_DEFAULT_VALUE,
        public readonly string $kafkaVersion = self::KAFKA_VERSION_DEFAULT_VALUE,
        /** @see https://kafka.apache.org/documentation/#replication */
        public readonly int $replicationFactor = self::REPLICATION_FACTOR_DEFAULT_VALUE,
        /** @see https://kafka.apache.org/documentation/#basic_ops_cluster_expansion */
        public readonly ?array $replicaAssignment = null,
        /** @see https://kafka.apache.org/documentation/#configuration */
        public readonly ?array $configEntries = null,
        public readonly int $maxMessageBytes = self::MAX_MESSAGE_BYTES_DEFAULT_VALUE,
        public readonly int|RequiredAcks $requiredAcks = RequiredAcks::TypeWaitForLocal,
        public readonly int $timeout = self::TIMEOUT_DEFAULT_VALUE,
        public readonly string|CompressionCodec $compressionCodec = CompressionCodec::Snappy,
        public readonly int $compressionLevel = self::COMPRESSION_LEVEL_DEFAULT_VALUE,
        public readonly bool $idempotent = self::IDEMPOTENT_DEFAULT_VALUE,
        public readonly int $heartbeatInterval = self::HEARTBEAT_INTERVAL_DEFAULT_VALUE,
        public readonly int $sessionTimeout = self::SESSION_TIMEOUT_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::Kafka, $name, $priority);

        \assert($this->topic !== '', 'Precondition [topic !== ""] failed');
        \assert($this->clientId !== '', 'Precondition [clientId !== ""] failed');
        \assert($this->kafkaVersion !== '', 'Precondition [kafkaVersion !== ""] failed');
        \assert($this->maxOpenRequests >= 1, 'Precondition [maxOpenRequests >= 1] failed');
        \assert($this->replicationFactor >= 1, 'Precondition [replicationFactor >= 1] failed');
        \assert($this->maxMessageBytes >= 1, 'Precondition [maxMessageBytes >= 1] failed');
        \assert($this->timeout >= 1, 'Precondition [timeout >= 1] failed');
        \assert($this->compressionLevel >= 1, 'Precondition [compressionLevel >= 1] failed');
        \assert($this->heartbeatInterval >= 1, 'Precondition [heartbeatInterval >= 1] failed');
    }

    public function toArray(): array
    {
        $info = [
            'topic' => $this->topic,
            'group_id' => $this->groupId,
            'max_open_requests' => $this->maxOpenRequests,
            'client_id' => $this->clientId,
            'version' => $this->kafkaVersion,
            'replication_factor' => $this->replicationFactor,
            'max_message_bytes' => $this->maxMessageBytes,
            'required_acks' => \is_int($this->requiredAcks) ? $this->requiredAcks : $this->requiredAcks->value,
            'timeout' => $this->timeout,
            'compression_codec' => \is_string($this->compressionCodec)
                ? $this->compressionCodec
                : $this->compressionCodec->value,
            'compression_level' => $this->compressionLevel,
            'idempotent' => $this->idempotent,
            'heartbeat_interval' => $this->heartbeatInterval,
            'session_timeout' => $this->sessionTimeout,
        ];

        if ($this->partitionsOffsets !== null) {
            foreach ($this->partitionsOffsets as $key => $offset) {
                $info['partitions_offsets'][$key] = $offset instanceof PartitionOffset ? $offset->value : $offset;
            }
        }

        if ($this->replicaAssignment !== null) {
            $info['replica_assignment'] = $this->replicaAssignment;
        }

        if ($this->configEntries !== null) {
            $info['config_entries'] = $this->configEntries;
        }

        return \array_merge(parent::toArray(), $info);
    }
}
