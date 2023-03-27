<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use DateInterval;
use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\Acks;
use Spiral\RoadRunner\Jobs\Queue\Kafka\CompressionCodec;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ProducerOptions;

final class ProducerOptionsTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $options = new ProducerOptions();

        $this->assertFalse($options->disableIdempotent);
        $this->assertSame(Acks::AllISRAck, $options->requiredAcks);
        $this->assertSame(1000012, $options->maxMessageBytes);
        $this->assertNull($options->requestTimeout);
        $this->assertNull($options->deliveryTimeout);
        $this->assertNull($options->transactionTimeout);
        $this->assertNull($options->compressionCodec);
    }

    public function testCustomValues(): void
    {
        $options = new ProducerOptions(
            true,
            Acks::NoAck,
            100,
            new DateInterval('PT5S'),
            new DateInterval('PT50S'),
            new DateInterval('PT20S'),
            CompressionCodec::Gzip
        );

        $this->assertTrue($options->disableIdempotent);
        $this->assertSame(Acks::NoAck, $options->requiredAcks);
        $this->assertSame(100, $options->maxMessageBytes);
        $this->assertEquals(new DateInterval('PT5S'), $options->requestTimeout);
        $this->assertEquals(new DateInterval('PT50S'), $options->deliveryTimeout);
        $this->assertEquals(new DateInterval('PT20S'), $options->transactionTimeout);
        $this->assertSame(CompressionCodec::Gzip, $options->compressionCodec);
    }

    public function testJsonSerialization(): void
    {
        $options = new ProducerOptions(
            true,
            Acks::AllISRAck,
            100,
            new DateInterval('PT5S'),
            new DateInterval('PT50S'),
            new DateInterval('PT20S'),
            CompressionCodec::Gzip
        );

        $this->assertSame(
            <<<'JSON'
{
    "disable_idempotent": true,
    "max_message_bytes": 100,
    "request_timeout": "5s",
    "delivery_timeout": "50s",
    "transaction_timeout": "20s",
    "required_acks": "AllISRAck",
    "compression_codec": "gzip"
}
JSON
            ,
            \json_encode($options, JSON_PRETTY_PRINT),
        );
    }
}