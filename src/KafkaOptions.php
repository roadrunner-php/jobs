<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Queue\Kafka\PartitionOffset;

/**
 * @psalm-import-type PartitionOffsetEnum from PartitionOffset
 */
final class KafkaOptions extends Options implements KafkaOptionsInterface
{
    /**
     * @var non-empty-string
     */
    public string $topic;

    /**
     * @var string
     */
    public string $metadata = self::DEFAULT_METADATA;

    /**
     * @var PartitionOffsetEnum
     */
    public int $offset = self::DEFAULT_OFFSET;

    /**
     * @var positive-int|0
     */
    public int $partition = self::DEFAULT_PARTITION;

    /**
     * @param non-empty-string $topic
     * @psalm-param 0|positive-int $delay
     * @psalm-param 0|positive-int $priority
     * @param bool $autoAck
     * @param string $metadata
     * @psalm-param PartitionOffsetEnum $offset
     * @param int $partition
     */
    public function __construct(
        string $topic,
        int $delay = self::DEFAULT_DELAY,
        int $priority = self::DEFAULT_PRIORITY,
        bool $autoAck = self::DEFAULT_AUTO_ACK,
        string $metadata = self::DEFAULT_METADATA,
        int $offset = self::DEFAULT_OFFSET,
        int $partition = self::DEFAULT_PARTITION
    ) {
        parent::__construct($delay, $priority, $autoAck);

        assert($topic !== '', 'Precondition [topic !== ""] failed');
        assert($partition >= 0, 'Precondition [partition >= 0] failed');

        $this->topic = $topic;
        $this->offset = $offset;
        $this->partition = $partition;
        $this->metadata = $metadata;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public static function from(OptionsInterface $options): self
    {
        $self = new self('default', $options->getDelay(), $options->getPriority(), $options->getAutoAck());

        if ($options instanceof KafkaOptionsInterface) {
            return $self
                ->withTopic($options->getTopic())
                ->withMetadata($options->getMetadata())
                ->withOffset($options->getOffset())
                ->withPartition($options->getPartition());
        }

        return $self;
    }

    public function merge(OptionsInterface $options): OptionsInterface
    {
        /** @var KafkaOptions $self */
        $self = parent::merge($options);

        if ($options instanceof KafkaOptionsInterface) {
            $self->topic = $options->getTopic();

            if (($metadata = $options->getMetadata()) !== self::DEFAULT_METADATA) {
                $self->metadata = $metadata;
            }

            if (($offset = $options->getOffset()) !== self::DEFAULT_OFFSET) {
                $self->offset = $offset;
            }

            if (($partition = $options->getPartition()) !== self::DEFAULT_PARTITION) {
                $self->partition = $partition;
            }
        }

        return $self;
    }

    /**
     * @psalm-immutable
     * @param non-empty-string $topic
     * @return $this
     */
    public function withTopic(string $topic): self
    {
        assert($topic !== '', 'Precondition [topic !== ""] failed');

        $self = clone $this;
        $self->topic = $topic;

        return $self;
    }

    public function getTopic(): string
    {
        assert($this->topic !== '', 'Precondition [topic !== ""] failed');

        return $this->topic;
    }

    /**
     * @psalm-immutable
     * @param string $metadata
     * @return $this
     */
    public function withMetadata(string $metadata): self
    {
        $self = clone $this;
        $self->metadata = $metadata;

        return $self;
    }

    public function getMetadata(): string
    {
        return $this->metadata;
    }

    /**
     * @psalm-immutable
     * @param PartitionOffsetEnum $offset
     * @return $this
     */
    public function withOffset(int $offset): self
    {
        $self = clone $this;
        $self->offset = $offset;

        return $self;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @psalm-immutable
     * @param positive-int|0 $offset
     * @return $this
     */
    public function withPartition(int $partition): self
    {
        assert($partition >= 0, 'Precondition [partition >= 0] failed');

        $self = clone $this;
        $self->partition = $partition;

        return $self;
    }

    public function getPartition(): int
    {
        assert($this->partition >= 0, 'Precondition [partition >= 0] failed');

        return $this->partition;
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'topic' => $this->topic,
            'metadata' => $this->metadata,
            'offset' => $this->offset,
            'partition' => $this->partition,
        ]);
    }
}
