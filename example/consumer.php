<?php

declare(strict_types=1);

use Spiral\RoadRunner\Jobs\Consumer;

require __DIR__ . '/../vendor/autoload.php';

$consumer = new Consumer();

while ($task = $consumer->waitTask()) {
    $task->complete();
}

