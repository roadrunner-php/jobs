<?php

declare(strict_types=1);

use Spiral\RoadRunner\Jobs\Queue;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue('test');

$task = $queue->dispatch(
    $queue->create('echo')
        ->withValue(static fn($arg) => print $arg)
);

dump($task->getId() . ' has been queued');


