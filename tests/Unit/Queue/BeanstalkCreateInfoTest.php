<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\BeanstalkCreateInfo;
use Spiral\RoadRunner\Jobs\Queue\Driver;

final class BeanstalkCreateInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $beanstalkCreateInfo = new BeanstalkCreateInfo('test',);

        $this->assertInstanceOf(BeanstalkCreateInfo::class, $beanstalkCreateInfo);
        $this->assertEquals(Driver::Beanstalk, $beanstalkCreateInfo->driver);
        $this->assertEquals('test', $beanstalkCreateInfo->name);
        $this->assertEquals(BeanstalkCreateInfo::PRIORITY_DEFAULT_VALUE, $beanstalkCreateInfo->priority);
        $this->assertEquals(BeanstalkCreateInfo::TUBE_PRIORITY_DEFAULT_VALUE, $beanstalkCreateInfo->tubePriority);
        $this->assertEquals(BeanstalkCreateInfo::TUBE_DEFAULT_VALUE, $beanstalkCreateInfo->tube);
        $this->assertEquals(BeanstalkCreateInfo::RESERVE_TIMEOUT_DEFAULT_VALUE, $beanstalkCreateInfo->reserveTimeout);
    }

    public function testBeanstalkCreateInfoCustomValues(): void
    {
        $name = 'test';
        $priority = 1;
        $tubePriority = 100;
        $tube = 'my-tube';
        $reserveTimeout = 30;

        $beanstalkCreateInfo = new BeanstalkCreateInfo(
            name: $name,
            priority: $priority,
            tubePriority: $tubePriority,
            tube: $tube,
            reserveTimeout: $reserveTimeout
        );

        $this->assertEquals(Driver::Beanstalk, $beanstalkCreateInfo->driver);
        $this->assertEquals($name, $beanstalkCreateInfo->name);
        $this->assertEquals($priority, $beanstalkCreateInfo->priority);
        $this->assertEquals($tubePriority, $beanstalkCreateInfo->tubePriority);
        $this->assertEquals($tube, $beanstalkCreateInfo->tube);
        $this->assertEquals($reserveTimeout, $beanstalkCreateInfo->reserveTimeout);
    }

    public function testToArray(): void
    {
        $name = 'test';
        $priority = 1;
        $tubePriority = 100;
        $tube = 'my-tube';
        $reserveTimeout = 30;

        $beanstalkCreateInfo = new BeanstalkCreateInfo(
            name: $name,
            priority: $priority,
            tubePriority: $tubePriority,
            tube: $tube,
            reserveTimeout: $reserveTimeout
        );

        $expectedArray = [
            'driver' => Driver::Beanstalk->value,
            'name' => $name,
            'priority' => $priority,
            'tube_priority' => $tubePriority,
            'tube' => $tube,
            'reserve_timeout' => $reserveTimeout,
        ];

        $this->assertEquals($expectedArray, $beanstalkCreateInfo->toArray());
    }
}
