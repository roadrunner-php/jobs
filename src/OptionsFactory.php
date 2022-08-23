<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Queue\Driver;

final class OptionsFactory
{
    public static function create(string $driver): OptionsInterface
    {
        switch ($driver) {
            case Driver::KAFKA:
                return new KafkaOptions('default');
            default:
                return new Options();
        }
    }
}
