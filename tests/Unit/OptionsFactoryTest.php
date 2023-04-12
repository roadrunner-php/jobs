<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsFactory;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Traversable;

final class OptionsFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider defaultDriversDataProvider
     */
    public function testCreateWithOptions(Driver $driver): void
    {
        $options = OptionsFactory::create($driver);
        $this->assertInstanceOf(Options::class, $options);
    }

    public function testCreateWithKafkaOptions(): void
    {
        $options = OptionsFactory::create(Driver::Kafka);

        $this->assertInstanceOf(KafkaOptions::class, $options);
        $this->assertEquals('default', $options->getTopic());
    }

    public static function defaultDriversDataProvider(): Traversable
    {
        yield [Driver::SQS];
        yield [Driver::AMQP];
        yield [Driver::Beanstalk];
        yield [Driver::BoltDB];
        yield [Driver::Memory];
        yield [Driver::NSQ];
        yield [Driver::NATS];
        yield [Driver::Redis];
    }
}
