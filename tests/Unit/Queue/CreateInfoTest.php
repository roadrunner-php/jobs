<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\CreateInfo;
use Spiral\RoadRunner\Jobs\Queue\Driver;

final class CreateInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        $this->assertInstanceOf(CreateInfo::class, $createInfo);
    }

    public function testDefaultPriority(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name');

        $this->assertEquals(CreateInfo::PRIORITY_DEFAULT_VALUE, $createInfo->priority);
    }

    public function testGetName(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        $this->assertEquals('name', $createInfo->getName());
    }

    public function testGetDriver(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);

        $this->assertEquals(Driver::Memory, $createInfo->getDriver());
    }

    public function testToArray(): void
    {
        $createInfo = new CreateInfo(Driver::Memory, 'name', 5);
        $expectedArray = [
            'name' => 'name',
            'driver' => Driver::Memory->value,
            'priority' => 5,
        ];

        $this->assertEquals($expectedArray, $createInfo->toArray());
    }
}
