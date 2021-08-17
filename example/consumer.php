<?php

declare(strict_types=1);

use Spiral\RoadRunner\Jobs\Consumer;

require __DIR__ . '/../vendor/autoload.php';

$consumer = new Consumer();

while ($task = $consumer->waitTask()) {
    if (random_int(0, 1)) {
        // Execute task
        $value = $task->getValue(0);
        $value('Hello World!');

        // Mark task as completed
        $task->complete();

        continue;
    }

    // Failed task (with example attempts header).
    $attempts = (int)$task->getHeaderLine('attempts');
    $delay = (int)$task->getHeaderLine('retry-delay');

    $task
        ->withDelay($delay * 2)
        ->withHeader('attempts', max(0, $attempts - 1))
        ->fail('Something went wrong', $attempts > 0)
    ;
}

