<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerOffset;
use Spiral\RoadRunner\Jobs\Queue\Kafka\OffsetType;

final class ConsumerOffsetTest extends TestCase
{
    public function testConstructor(): void
    {
        $type = OffsetType::AtStart;
        $value = 123;

        $consumerOffset = new ConsumerOffset($type, $value);

        $this->assertSame($type, $consumerOffset->type);
        $this->assertSame($value, $consumerOffset->value);
    }

    public function testJsonSerialize(): void
    {
        $type = OffsetType::AfterMilli;
        $value = 456;

        $consumerOffset = new ConsumerOffset($type, $value);

        $expected = [
            'type' => $type->value,
            'value' => $value,
        ];

        $this->assertEquals($expected, $consumerOffset->jsonSerialize());
    }
}