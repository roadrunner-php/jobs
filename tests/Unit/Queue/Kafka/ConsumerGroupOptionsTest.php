<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use Spiral\RoadRunner\Jobs\Queue\Kafka\ConsumerGroupOptions;
use Testo\Assert;
use Testo\Test;

#[Test]
final class ConsumerGroupOptionsTest
{
    public function testJsonSerialize(): void
    {
        $options = new ConsumerGroupOptions('my-group', true);

        $expected = [
            'group_id' => 'my-group',
            'block_rebalance_on_poll' => true,
        ];

        Assert::equals($options->jsonSerialize(), $expected);
    }

    public function testDefaultBlockRebalanceOnPoll(): void
    {
        $options = new ConsumerGroupOptions('my-group');

        Assert::false($options->blockRebalanceOnPoll);
    }

    public function testNullableGroupId(): void
    {
        $options = new ConsumerGroupOptions();

        Assert::null($options->groupId);
    }
}
