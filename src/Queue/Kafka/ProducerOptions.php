<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

use DateInterval;

final class ProducerOptions implements \JsonSerializable
{
    public const DEFAULT_DISABLE_IDEMPOTENT_DEFAULT_VALUE = false;
    public const DEFAULT_MAX_MESSAGE_BYTES_DEFAULT_VALUE = 1_000_012;
    public const DEFAULT_REQUEST_TIMEOUT_DEFAULT_VALUE = 'PT10S';
    public const DEFAULT_DELIVERY_TIMEOUT_DEFAULT_VALUE = 'PT100S';
    public const DEFAULT_TRANSACTION_TIMEOUT_DEFAULT_VALUE = 'PT40S';

    /**
     * @param bool $disableIdempotent Disable_idempotent disables idempotent produce requests, opting out of Kafka
     * server-side deduplication in the face of reissued requests due to transient network problems.
     *
     * @param Acks $requiredAcks Sets the required acks for produced records.
     *
     * @param positive-int $maxMessageBytes upper bounds the size of a record batch, overriding the default 1,000,012 bytes.
     * This mirrors Kafka's max.message.bytes.
     *
     * @param DateInterval $requestTimeout sets how long Kafka broker's are allowed to respond produce requests,
     * overriding the default 10s. If a broker exceeds this duration, it will reply with a request timeout error.
     *
     * @param DateInterval $deliveryTimeout sets a rough time of how long a record can sit around in a batch before
     * timing out, overriding the unlimited default. If idempotency is enabled (as it is by default), this option is
     * only enforced if it is safe to do so without creating invalid sequence numbers.
     *
     * @param DateInterval $transactionTimeout sets the allowed for a transaction, overriding the default 40s. It is
     * a good idea to keep this less than a group's session timeout.
     *
     * @param CompressionCodec|null $compressionCodec sets the compression codec to use for producing records.
     * Default is chosen in the order preferred based on broker support.
     */
    public function __construct(
        public readonly bool $disableIdempotent = self::DEFAULT_DISABLE_IDEMPOTENT_DEFAULT_VALUE,
        public readonly Acks $requiredAcks = Acks::AllISRAck,
        public readonly int $maxMessageBytes = self::DEFAULT_MAX_MESSAGE_BYTES_DEFAULT_VALUE,
        public readonly DateInterval $requestTimeout = new DateInterval(self::DEFAULT_REQUEST_TIMEOUT_DEFAULT_VALUE),
        public readonly DateInterval $deliveryTimeout = new DateInterval(self::DEFAULT_DELIVERY_TIMEOUT_DEFAULT_VALUE),
        public readonly DateInterval $transactionTimeout = new DateInterval(
            self::DEFAULT_TRANSACTION_TIMEOUT_DEFAULT_VALUE
        ),
        public readonly ?CompressionCodec $compressionCodec = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'disable_idempotent' => $this->disableIdempotent,
            'required_acks' => $this->requiredAcks->value,
            'max_message_bytes' => $this->maxMessageBytes,
            'request_timeout' => $this->convertDateIntervalToString($this->requestTimeout),
            'delivery_timeout' => $this->convertDateIntervalToString($this->deliveryTimeout),
            'transaction_timeout' => $this->convertDateIntervalToString($this->transactionTimeout),
        ];

        if ($this->compressionCodec !== null) {
            $data['compression_codec'] = $this->compressionCodec->value;
        }

        return $data;
    }

    private function convertDateIntervalToString(DateInterval $interval): string
    {
        return $interval->format('%s') . 's';
    }
}