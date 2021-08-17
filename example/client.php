<?php

declare(strict_types=1);

use Spiral\RoadRunner\Jobs\Queue;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue('test');

// Create task prototype with default headers
$prototype = $queue->create('echo')
    ->withHeader('attempts', 4)
    ->withHeader('retry-delay', 10)
;

// Execute "echo" task with Closure as payload
$task = $queue->dispatch(
    $prototype->withValue(static fn($arg) => print $arg)
);

dump($task->getId() . ' has been queued');


