<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\Driver;
use Spiral\RoadRunner\Jobs\Queue\MemoryCreateInfo;
use Testo\Assert;
use Testo\Test;

#[Test]
final class MemoryCreateInfoTest
{
    public function testConstructor(): void
    {
        $memoryCreateInfo = new MemoryCreateInfo('test-name');

        Assert::equals($memoryCreateInfo->driver, Driver::Memory);
        Assert::equals($memoryCreateInfo->name, 'test-name');
        Assert::equals($memoryCreateInfo->priority, MemoryCreateInfo::PRIORITY_DEFAULT_VALUE);
        Assert::equals($memoryCreateInfo->prefetch, MemoryCreateInfo::PREFETCH_DEFAULT_VALUE);
    }

    public function testConstructorWithParameters(): void
    {
        $memoryCreateInfo = new MemoryCreateInfo('test-name', 50, 20);

        Assert::equals($memoryCreateInfo->priority, 50);
        Assert::equals($memoryCreateInfo->prefetch, 20);
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

        Assert::equals($memoryCreateInfo->toArray(), $expectedArray);
    }
}
