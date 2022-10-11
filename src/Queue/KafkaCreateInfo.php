<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Spiral\RoadRunner\Jobs\Queue\Kafka\CompressionCodec;
use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\RequiredAcks;

/**
 * The DTO to create the Kafka driver.
 *
 * @psalm-import-type CreateInfoArrayType from CreateInfoInterface
 * @psalm-import-type CompressionCodecEnum from CompressionCodec
 * @psalm-import-type RequiredAcksEnum from RequiredAcks
 * @psalm-import-type PartitionOffsetEnum from PartitionOffset
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
     * @var RequiredAcksEnum
     */
    public const REQUIRED_ACKS_DEFAULT_VALUE = RequiredAcks::TYPE_WAIT_FOR_LOCAL;

    /**
     * @var positive-int
     */
    public const TIMEOUT_DEFAULT_VALUE = 10;

    /**
     * @var CompressionCodecEnum
     */
    public const COMPRESSION_CODEC_DEFAULT_VALUE = CompressionCodec::CODEC_SNAPPY;

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
     * @see https://kafka.apache.org/intro#intro_concepts_and_terms
     * @var non-empty-string
     */
    public string $topic;

    public string $groupId = self::GROUP_ID_DEFAULT_VALUE;

    /**
     * @var array<positive-int, positive-int|PartitionOffsetEnum>|null
     */
    public ?array $partitionsOffsets;

    /**
     * @see https://kafka.apache.org/28/documentation.html#producerconfigs_max.in.flight.requests.per.connection
     * @var positive-int
     */
    public int $maxOpenRequests = self::MAX_OPEN_REQUESTS_DEFAULT_VALUE;

    /**
     * @var non-empty-string
     */
    public string $clientId = self::CLIENT_ID_DEFAULT_VALUE;

    /**
     * @var non-empty-string
     */
    public string $kafkaVersion = self::KAFKA_VERSION_DEFAULT_VALUE;

    /**
     * @see https://kafka.apache.org/documentation/#replication
     * @var positive-int
     */
    public int $replicationFactor = self::REPLICATION_FACTOR_DEFAULT_VALUE;

    /**
     * @see https://kafka.apache.org/documentation/#basic_ops_cluster_expansion
     * @var array<positive-int, array<positive-int>>|null
     */
    public ?array $replicaAssignment;

    /**
     * @see https://kafka.apache.org/documentation/#configuration
     * @var array<non-empty-string, mixed>|null
     */
    public ?array $configEntries;

    /**
     * @var positive-int
     */
    public int $maxMessageBytes = self::MAX_MESSAGE_BYTES_DEFAULT_VALUE;

    /**
     * @var RequiredAcksEnum
     */
    public int $requiredAcks = self::REQUIRED_ACKS_DEFAULT_VALUE;

    /**
     * @var positive-int
     */
    public int $timeout = self::TIMEOUT_DEFAULT_VALUE;

    /**
     * @var CompressionCodecEnum
     */
    public string $compressionCodec = self::COMPRESSION_CODEC_DEFAULT_VALUE;

    /**
     * @var positive-int
     */
    public int $compressionLevel = self::COMPRESSION_LEVEL_DEFAULT_VALUE;

    /**
     * @var bool
     */
    public bool $idempotent = self::IDEMPOTENT_DEFAULT_VALUE;

    /**
     * @var positive-int
     */
    public int $heartbeatInterval = self::HEARTBEAT_INTERVAL_DEFAULT_VALUE;

    /**
     * @var positive-int
     */
    public int $sessionTimeout = self::SESSION_TIMEOUT_DEFAULT_VALUE;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $topic
     * @param positive-int $priority
     * @param string $groupId
     * @param array<positive-int, positive-int|PartitionOffsetEnum>|null $partitionsOffsets
     * @param positive-int $maxOpenRequests
     * @param non-empty-string $clientId
     * @param non-empty-string $kafkaVersion
     * @param positive-int $replicationFactor
     * @param array<positive-int, array<positive-int>>|null $replicaAssignment
     * @param array<non-empty-string, mixed>|null $configEntries
     * @param positive-int $maxMessageBytes
     * @psalm-param RequiredAcksEnum $requiredAcks
     * @param positive-int $timeout
     * @psalm-param CompressionCodecEnum $compressionCodec
     * @param positive-int $compressionLevel
     * @param bool $idempotent
     * @param positive-int $heartbeatInterval
     * @param positive-int $sessionTimeout
     */
    public function __construct(
        string $name,
        string $topic,
        int $priority = self::PRIORITY_DEFAULT_VALUE,
        string $groupId = self::GROUP_ID_DEFAULT_VALUE,
        ?array $partitionsOffsets = null,
        int $maxOpenRequests = self::MAX_OPEN_REQUESTS_DEFAULT_VALUE,
        string $clientId = self::CLIENT_ID_DEFAULT_VALUE,
        string $kafkaVersion = self::KAFKA_VERSION_DEFAULT_VALUE,
        int $replicationFactor = self::REPLICATION_FACTOR_DEFAULT_VALUE,
        ?array $replicaAssignment = null,
        ?array $configEntries = null,
        int $maxMessageBytes = self::MAX_MESSAGE_BYTES_DEFAULT_VALUE,
        int $requiredAcks = self::REQUIRED_ACKS_DEFAULT_VALUE,
        int $timeout = self::TIMEOUT_DEFAULT_VALUE,
        string $compressionCodec = self::COMPRESSION_CODEC_DEFAULT_VALUE,
        int $compressionLevel = self::COMPRESSION_LEVEL_DEFAULT_VALUE,
        bool $idempotent = self::IDEMPOTENT_DEFAULT_VALUE,
        int $heartbeatInterval = self::HEARTBEAT_INTERVAL_DEFAULT_VALUE,
        int $sessionTimeout = self::SESSION_TIMEOUT_DEFAULT_VALUE
    ) {
        parent::__construct(Driver::KAFKA, $name, $priority);

        assert($topic !== '', 'Precondition [topic !== ""] failed');
        assert($clientId !== '', 'Precondition [clientId !== ""] failed');
        assert($kafkaVersion !== '', 'Precondition [kafkaVersion !== ""] failed');
        assert($maxOpenRequests >= 1, 'Precondition [maxOpenRequests >= 1] failed');
        assert($replicationFactor >= 1, 'Precondition [replicationFactor >= 1] failed');
        assert($maxMessageBytes >= 1, 'Precondition [maxMessageBytes >= 1] failed');
        assert($timeout >= 1, 'Precondition [timeout >= 1] failed');
        assert($compressionCodec !== '', 'Precondition [compressionCodec !== ""] failed');
        assert($compressionLevel >= 1, 'Precondition [compressionLevel >= 1] failed');
        assert($heartbeatInterval >= 1, 'Precondition [heartbeatInterval >= 1] failed');

        $this->topic = $topic;
        $this->groupId = $groupId;
        $this->partitionsOffsets = $partitionsOffsets;
        $this->maxOpenRequests = $maxOpenRequests;
        $this->clientId = $clientId;
        $this->kafkaVersion = $kafkaVersion;
        $this->replicationFactor = $replicationFactor;
        $this->replicaAssignment = $replicaAssignment;
        $this->configEntries = $configEntries;
        $this->maxMessageBytes = $maxMessageBytes;
        $this->requiredAcks = $requiredAcks;
        $this->timeout = $timeout;
        $this->compressionCodec = $compressionCodec;
        $this->compressionLevel = $compressionLevel;
        $this->idempotent = $idempotent;
        $this->heartbeatInterval = $heartbeatInterval;
        $this->sessionTimeout = $sessionTimeout;
    }

    /**
     * {@inheritDoc}
     */
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
            'required_acks' => $this->requiredAcks,
            'timeout' => $this->timeout,
            'compression_codec' => $this->compressionCodec,
            'compression_level' => $this->compressionLevel,
            'idempotent' => $this->idempotent,
            'heartbeat_interval' => $this->heartbeatInterval,
            'session_timeout' => $this->sessionTimeout,
        ];

        if ($this->partitionsOffsets !== null) {
            $info['partitions_offsets'] = $this->partitionsOffsets;
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
