<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;
use Testo\Assert;
use Testo\Test;

#[Test]
final class ConsumerOffsetTest
{
    public function testConstructor(): void
    {
        $type = OffsetType::AtStart;
        $value = 123;

        $consumerOffset = new ConsumerOffset($type, $value);

        Assert::same($consumerOffset->type, $type);
        Assert::same($consumerOffset->value, $value);
    }

    public function testJsonSerialize(): void
    {
        Assert::equals(\json_encode(new ConsumerOffset(OffsetType::AfterMilli, 456)), '{"type":"AfterMilli","value":456}');
    }
}
