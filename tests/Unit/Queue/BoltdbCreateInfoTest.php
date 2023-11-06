<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\BoltdbCreateInfo;

final class BoltdbCreateInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $name = 'test_queue';
        $file = 'custom_file.db';
        $priority = 2;
        $prefetch = 5000;
        $permissions = 0666;

        $boltdbCreateInfo = new BoltdbCreateInfo($name, $file, $priority, $prefetch, $permissions);

        $this->assertEquals($name, $boltdbCreateInfo->name);
        $this->assertEquals($file, $boltdbCreateInfo->file);
        $this->assertEquals($priority, $boltdbCreateInfo->priority);
        $this->assertEquals($prefetch, $boltdbCreateInfo->prefetch);
        $this->assertEquals($permissions, $boltdbCreateInfo->permissions);
    }

    public function testDefaultValues(): void
    {
        $boltdbCreateInfo = new BoltdbCreateInfo('test_queue');

        $this->assertEquals(BoltdbCreateInfo::PRIORITY_DEFAULT_VALUE, $boltdbCreateInfo->priority);
        $this->assertEquals(BoltdbCreateInfo::PREFETCH_DEFAULT_VALUE, $boltdbCreateInfo->prefetch);
        $this->assertEquals(BoltdbCreateInfo::FILE_DEFAULT_VALUE, $boltdbCreateInfo->file);
        $this->assertEquals(BoltdbCreateInfo::PERMISSIONS_DEFAULT_VALUE, $boltdbCreateInfo->permissions);
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
            'permissions' => 0777,
        ];

        $this->assertEquals($expectedArray, $boltdbCreateInfo->toArray());
    }
}
