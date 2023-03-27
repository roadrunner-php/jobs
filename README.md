# RoadRunner Jobs Plugin

[![PHP Version Require](https://poser.pugx.org/spiral/roadrunner-jobs/require/php)](https://packagist.org/packages/spiral/roadrunner-jobs)
[![Latest Stable Version](https://poser.pugx.org/spiral/spiral/roadrunner-jobs/v/stable)](https://packagist.org/packages/spiral/roadrunner-jobs)
[![phpunit](https://github.com/spiral/roadrunner-jobs/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral/roadrunner-jobs/actions)
[![psalm](https://github.com/spiral/roadrunner-jobs/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral/roadrunner-jobs/actions)
[![Codecov](https://codecov.io/gh/spiral/roadrunner-jobs/branch/3.x/graph/badge.svg)](https://codecov.io/gh/spiral/roadrunner-jobs/)
[![Total Downloads](https://poser.pugx.org/spiral/roadrunner-jobs/downloads)](https://packagist.org/packages/spiral/roadrunner-jobs)
[![StyleCI](https://github.styleci.io/repos/447581540/shield)](https://github.styleci.io/repos/447581540)
<a href="https://discord.gg/8bZsjYhVVk"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

This repository contains the codebase PHP bridge using RoadRunner Jobs plugin.

## Installation

To install application server and Jobs codebase

```bash
composer require spiral/roadrunner-jobs
```

You can use the convenient installer to download the latest available compatible version of RoadRunner assembly:

```bash
composer require spiral/roadrunner-cli --dev
vendor/bin/rr get
```

## Configuration

First you need to add at least one jobs adapter to your RoadRunner configuration. For example, such a configuration 
would be quite feasible to run:

```yaml
rpc:
  listen: tcp://127.0.0.1:6001

server:
  command: php consumer.php
  relay: pipes

jobs:
  consume: [ "local" ]
  pipelines:
    local:
      driver: memory
      config:
        priority: 10
        prefetch: 10000
```

> **Note**
> Read more about all available drivers on the [documentation](https://roadrunner.dev/docs/plugins-jobs/2.x/en) page.

After starting the server with this configuration, one driver named `local` will be available to you.

## Usage

### Producer

The following code will allow writing and reading an arbitrary value from the RoadRunner server.

```php
<?php

use Spiral\RoadRunner\Jobs\Jobs;
use Spiral\Goridge\RPC\RPC;

require __DIR__ . '/vendor/autoload.php';

// Jobs service
$jobs = new Jobs(RPC::create('tcp://127.0.0.1:6001'));

// Select "local" pipeline from jobs
$queue = $jobs->connect('local');

// Create task prototype with default headers
$task = $queue->create('ping', '{"site": "https://example.com"}') // Create task with "echo" name
    ->withHeader('attempts', 4) // Number of attempts to execute the task
    ->withHeader('retry-delay', 10); // Delay between attempts

// Push "echo" task to the queue
$task = $queue->dispatch($task);

var_dump($task->getId() . ' has been queued');
```

### Consumer

The following code will allow you to read and process the task from the RoadRunner server.

```php
<?php

use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

require __DIR__ . '/vendor/autoload.php';

$consumer = new Spiral\RoadRunner\Jobs\Consumer();

/** @var Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface $task */
while ($task = $consumer->waitTask()) {
    try {
        $name = $task->getName(); // "ping"
        $queue = $task->getQueue(); // "local"
        $driver = $queue->getDriver(); // "memory"
        $payload = $task->getPayload(); // {"site": "https://example.com"}
    
        // Process task

        $task->complete();
    } catch (\Throwable $e) {
        $task->fail($e, requeue: true);
    }
}
```

<a href="https://spiral.dev/">
<img src="https://user-images.githubusercontent.com/773481/220979012-e67b74b5-3db1-41b7-bdb0-8a042587dedc.jpg" alt="try Spiral Framework" />
</a>

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
