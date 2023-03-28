<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerGroupOptions;

final class ConsumerGroupOptionsTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $options = new ConsumerGroupOptions('my-group', true);

        $expected = [
            'group_id' => 'my-group',
            'block_rebalance_on_poll' => true,
        ];

        $this->assertEquals($expected, $options->jsonSerialize());
    }

    public function testDefaultBlockRebalanceOnPoll(): void
    {
        $options = new ConsumerGroupOptions('my-group');

        $this->assertFalse($options->blockRebalanceOnPoll);
    }

    public function testNullableGroupId(): void
    {
        $options = new ConsumerGroupOptions();

        $this->assertNull($options->groupId);
    }
}