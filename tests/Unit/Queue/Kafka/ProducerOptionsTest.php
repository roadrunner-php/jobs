<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Spiral\RoadRunner\Jobs\Queue\Kafka\Acks;
use Spiral\RoadRunner\Jobs\Queue\Kafka\CompressionCodec;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;
use Testo\Assert;
use Testo\Test;

#[Test]
final class ProducerOptionsTest
{
    public function testDefaultValues(): void
    {
        $options = new ProducerOptions();

        Assert::false($options->disableIdempotent);
        Assert::same($options->requiredAcks, Acks::AllISRAck);
        Assert::same($options->maxMessageBytes, 1000012);
        Assert::null($options->requestTimeout);
        Assert::null($options->deliveryTimeout);
        Assert::null($options->transactionTimeout);
        Assert::null($options->compressionCodec);
    }

    public function testCustomValues(): void
    {
        $options = new ProducerOptions(
            true,
            Acks::NoAck,
            100,
            new \DateInterval('PT5S'),
            new \DateInterval('PT50S'),
            new \DateInterval('PT20S'),
            CompressionCodec::Gzip,
        );

        Assert::true($options->disableIdempotent);
        Assert::same($options->requiredAcks, Acks::NoAck);
        Assert::same($options->maxMessageBytes, 100);
        Assert::equals((array) $options->requestTimeout, (array) new \DateInterval('PT5S'));
        Assert::equals((array) $options->deliveryTimeout, (array) new \DateInterval('PT50S'));
        Assert::equals((array) $options->transactionTimeout, (array) new \DateInterval('PT20S'));
        Assert::same($options->compressionCodec, CompressionCodec::Gzip);
    }

    public function testJsonSerialization(): void
    {
        $options = new ProducerOptions(
            true,
            Acks::AllISRAck,
            100,
            new \DateInterval('PT5S'),
            new \DateInterval('PT50S'),
            new \DateInterval('PT20S'),
            CompressionCodec::Gzip,
        );

        Assert::same(\json_encode($options, JSON_PRETTY_PRINT), <<<'JSON'
{
    "disable_idempotent": true,
    "max_message_bytes": 100,
    "request_timeout": "5s",
    "delivery_timeout": "50s",
    "transaction_timeout": "20s",
    "required_acks": "AllISRAck",
    "compression_codec": "gzip"
}
JSON);
    }
}
