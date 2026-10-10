<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\BoltdbCreateInfo;
use Testo\Assert;
use Testo\Test;

#[Test]
final class BoltdbCreateInfoTest
{
    public function testConstructor(): void
    {
        $name = 'test_queue';
        $file = 'custom_file.db';
        $priority = 2;
        $prefetch = 5000;
        $permissions = 0666;

        $boltdbCreateInfo = new BoltdbCreateInfo($name, $file, $priority, $prefetch, $permissions);

        Assert::equals($boltdbCreateInfo->name, $name);
        Assert::equals($boltdbCreateInfo->file, $file);
        Assert::equals($boltdbCreateInfo->priority, $priority);
        Assert::equals($boltdbCreateInfo->prefetch, $prefetch);
        Assert::equals($boltdbCreateInfo->permissions, $permissions);
    }

    public function testDefaultValues(): void
    {
        $boltdbCreateInfo = new BoltdbCreateInfo('test_queue');

        Assert::equals($boltdbCreateInfo->priority, BoltdbCreateInfo::PRIORITY_DEFAULT_VALUE);
        Assert::equals($boltdbCreateInfo->prefetch, BoltdbCreateInfo::PREFETCH_DEFAULT_VALUE);
        Assert::equals($boltdbCreateInfo->file, BoltdbCreateInfo::FILE_DEFAULT_VALUE);
        Assert::equals($boltdbCreateInfo->permissions, BoltdbCreateInfo::PERMISSIONS_DEFAULT_VALUE);
    }

    public function testToArray(): void
    {
        $name = 'test_queue';
        $file = 'custom_file.db';
        $priority = 2;
        $prefetch = 5000;

        $boltdbCreateInfo = new BoltdbCreateInfo($name, $file, $priority, $prefetch);

        $expectedArray = [
            'driver' => 'boltdb',
            'name' => $name,
            'priority' => $priority,
            'prefetch' => $prefetch,
            'file' => $file,
            'permissions' => 0755,
        ];

        Assert::equals($boltdbCreateInfo->toArray(), $expectedArray);
    }
}
