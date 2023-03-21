<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsFactory;
use Spiral\RoadRunner\Jobs\Queue\Driver;

final class OptionsFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider defaultDriversDataProvider
     */
    public function testCreateWithOptions(string $driver)
    {
        $options = OptionsFactory::create($driver);
        $this->assertInstanceOf(Options::class, $options);
    }

    public function testCreateWithKafkaOptions()
    {
        $options = OptionsFactory::create(Driver::KAFKA);

        $this->assertInstanceOf(KafkaOptions::class, $options);
        $this->assertEquals('default', $options->getTopic());
    }

    public function defaultDriversDataProvider()
    {
        return [
            [Driver::SQS],
            [Driver::AMQP],
            [Driver::BEANSTALK],
            [Driver::BOLTDB],
            [Driver::MEMORY],
            [Driver::NSQ],
            [Driver::NATS],
            [Driver::REDIS]
        ];
    }
}