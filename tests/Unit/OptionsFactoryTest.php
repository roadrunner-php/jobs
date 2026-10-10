<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\KafkaOptions;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsFactory;
use Spiral\RoadRunner\Jobs\Queue\Driver;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

#[Test]
final class OptionsFactoryTest
{
    public static function defaultDriversDataProvider(): \Traversable
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

    #[DataProvider('defaultDriversDataProvider')]
    public function testCreateWithOptions(Driver $driver): void
    {
        $options = OptionsFactory::create($driver);
        Assert::instanceOf($options, Options::class);
    }

    public function testCreateWithKafkaOptions(): void
    {
        $options = OptionsFactory::create(Driver::Kafka);

        Assert::instanceOf($options, KafkaOptions::class);
        Assert::equals($options->getTopic(), 'default');
    }
}
