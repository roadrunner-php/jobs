<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\CreateInfo;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Testo\Assert;
use Testo\Test;

#[Test]
final class CreateInfoTest
{
    public function testConstructor(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        Assert::instanceOf($createInfo, CreateInfo::class);
    }

    public function testDefaultPriority(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name');

        Assert::equals($createInfo->priority, CreateInfo::PRIORITY_DEFAULT_VALUE);
    }

    public function testGetName(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        Assert::equals($createInfo->getName(), 'name');
    }

    public function testGetDriver(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        Assert::equals($createInfo->getDriver(), Driver::Memory);
    }

    public function testToArray(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);
        $expectedArray = [
            'name' => 'name',
            'driver' => Driver::Memory->value,
            'priority' => 5,
        ];

        Assert::equals($createInfo->toArray(), $expectedArray);
    }
}
