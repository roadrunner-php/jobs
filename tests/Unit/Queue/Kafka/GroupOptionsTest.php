<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\GroupOptions;

final class GroupOptionsTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $options = new GroupOptions('my-group', true);

        $expected = [
            'group_id' => 'my-group',
            'block_rebalance_on_poll' => true,
        ];

        $this->assertEquals($expected, $options->jsonSerialize());
    }

    public function testDefaultBlockRebalanceOnPoll(): void
    {
        $options = new GroupOptions('my-group');

        $this->assertFalse($options->blockRebalanceOnPoll);
    }

    public function testNullableGroupId(): void
    {
        $options = new GroupOptions();

        $this->assertNull($options->groupId);
    }
}