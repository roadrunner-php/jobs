<?php

declare(strict_types=1);

use Spiral\RoadRunner\Jobs\Queue;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue('test');

$task = $queue->create('echo')
    ->with(static fn($arg) => print $arg)
    ->await(4)
    ->dispatch();

dump($task->getId() . ' has been queued');


