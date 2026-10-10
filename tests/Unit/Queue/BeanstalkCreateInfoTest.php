<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue;

use Spiral\RoadRunner\Jobs\Queue\BeanstalkCreateInfo;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Testo\Assert;
use Testo\Test;

#[Test]
final class BeanstalkCreateInfoTest
{
    public function testConstructor(): void
    {
        $beanstalkCreateInfo = new BeanstalkCreateInfo('test', );

        Assert::instanceOf($beanstalkCreateInfo, BeanstalkCreateInfo::class);
        Assert::equals($beanstalkCreateInfo->driver, Driver::Beanstalk);
        Assert::equals($beanstalkCreateInfo->name, 'test');
        Assert::equals($beanstalkCreateInfo->priority, BeanstalkCreateInfo::PRIORITY_DEFAULT_VALUE);
        Assert::equals($beanstalkCreateInfo->tubePriority, BeanstalkCreateInfo::TUBE_PRIORITY_DEFAULT_VALUE);
        Assert::equals($beanstalkCreateInfo->tube, BeanstalkCreateInfo::TUBE_DEFAULT_VALUE);
        Assert::equals($beanstalkCreateInfo->reserveTimeout, BeanstalkCreateInfo::RESERVE_TIMEOUT_DEFAULT_VALUE);
        Assert::equals($beanstalkCreateInfo->consumeAll, BeanstalkCreateInfo::CONSUME_ALL_DEFAULT_VALUE);
    }

    public function testBeanstalkCreateInfoCustomValues(): void
    {
        $name = 'test';
        $priority = 1;
        $tubePriority = 100;
        $tube = 'my-tube';
        $reserveTimeout = 30;
        $consumeAll = true;

        $beanstalkCreateInfo = new BeanstalkCreateInfo(
            name: $name,
            priority: $priority,
            tubePriority: $tubePriority,
            tube: $tube,
            reserveTimeout: $reserveTimeout,
            consumeAll: $consumeAll,
        );

        Assert::equals($beanstalkCreateInfo->driver, Driver::Beanstalk);
        Assert::equals($beanstalkCreateInfo->name, $name);
        Assert::equals($beanstalkCreateInfo->priority, $priority);
        Assert::equals($beanstalkCreateInfo->tubePriority, $tubePriority);
        Assert::equals($beanstalkCreateInfo->tube, $tube);
        Assert::equals($beanstalkCreateInfo->reserveTimeout, $reserveTimeout);
        Assert::equals($beanstalkCreateInfo->consumeAll, $consumeAll);
    }

    public function testToArray(): void
    {
        $name = 'test';
        $priority = 1;
        $tubePriority = 100;
        $tube = 'my-tube';
        $reserveTimeout = 30;
        $consumeAll = true;

        $beanstalkCreateInfo = new BeanstalkCreateInfo(
            name: $name,
            priority: $priority,
            tubePriority: $tubePriority,
            tube: $tube,
            reserveTimeout: $reserveTimeout,
            consumeAll: $consumeAll,
        );

        $expectedArray = [
            'driver' => Driver::Beanstalk->value,
            'name' => $name,
            'priority' => $priority,
            'tube_priority' => $tubePriority,
            'tube' => $tube,
            'reserve_timeout' => $reserveTimeout,
            'consume_all' => $consumeAll,
        ];

        Assert::equals($beanstalkCreateInfo->toArray(), $expectedArray);
    }
}
