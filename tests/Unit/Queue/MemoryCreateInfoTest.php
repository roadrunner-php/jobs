<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\MemoryCreateInfo;

final class MemoryCreateInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $memoryCreateInfo = new MemoryCreateInfo('test-name');

        $this->assertEquals(Driver::Memory, $memoryCreateInfo->driver);
        $this->assertEquals('test-name', $memoryCreateInfo->name);
        $this->assertEquals(MemoryCreateInfo::PRIORITY_DEFAULT_VALUE, $memoryCreateInfo->priority);
        $this->assertEquals(MemoryCreateInfo::PREFETCH_DEFAULT_VALUE, $memoryCreateInfo->prefetch);
    }

    public function testConstructorWithParameters(): void
    {
        $memoryCreateInfo = new MemoryCreateInfo('test-name', 50, 20);

        $this->assertEquals(50, $memoryCreateInfo->priority);
        $this->assertEquals(20, $memoryCreateInfo->prefetch);
    }

    public function testToArray(): void
    {
        $memoryCreateInfo = new MemoryCreateInfo('test-name', 50, 20);

        $expectedArray = [
            'driver' => Driver::Memory->value,
            'name' => 'test-name',
            'priority' => 50,
            'prefetch' => 20,
        ];

        $this->assertEquals($expectedArray, $memoryCreateInfo->toArray());
    }
}
