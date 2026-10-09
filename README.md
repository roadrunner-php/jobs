<p align="center">
    <a href="https://roadrunner.dev"><picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://github.com/roadrunner-server/.github/assets/8040338/e6bde856-4ec6-4a52-bd5b-bfe78736c1ff">
        <img alt="RoadRunner" src="https://github.com/roadrunner-server/.github/assets/8040338/040fb694-1dd3-4865-9d29-8e0748c2c8b8" style="width: 6in; display: block">
    </picture></a>
</p>

<p align="center">PHP client for RoadRunner Jobs (Queues) plugin</p>

<div align="center">

[![Documentation](https://img.shields.io/badge/Documentation-blue?style=for-the-badge&logo=gitbook&logoColor=white)](https://docs.roadrunner.dev/docs/queues-and-jobs/overview-queues)
[![Sponsor](https://img.shields.io/static/v1?style=for-the-badge&label=&message=Sponsor&logo=githubsponsors&logoColor=white&color=%23EA4AAA)](https://github.com/sponsors/roadrunner-server)

[![Psalm Level](https://shepherd.dev/github/roadrunner-php/jobs/level.svg)](https://shepherd.dev/github/roadrunner-php/jobs)
[![Type Coverage](https://shepherd.dev/github/roadrunner-php/jobs/coverage.svg)](https://shepherd.dev/github/roadrunner-php/jobs)

</div>

<br />

This package lets PHP applications work with the [RoadRunner Jobs plugin](https://docs.roadrunner.dev/docs/queues-and-jobs/overview-queues): create and manage pipelines, push tasks to queues and consume them in RoadRunner workers.

## Get Started

### Installation

```bash
composer require spiral/roadrunner-jobs
```

[![PHP](https://img.shields.io/packagist/php-v/spiral/roadrunner-jobs.svg?style=flat-square&logo=php)](https://packagist.org/packages/spiral/roadrunner-jobs)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral/roadrunner-jobs.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/spiral/roadrunner-jobs)
[![License](https://img.shields.io/packagist/l/spiral/roadrunner-jobs.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral/roadrunner-jobs.svg?style=flat-square)](https://packagist.org/packages/spiral/roadrunner-jobs/stats)

You can use the convenient installer to download the latest available compatible version of RoadRunner assembly:

```bash
composer require spiral/roadrunner-cli --dev
vendor/bin/rr get
```

### Configuration

First you need to add at least one jobs adapter to your RoadRunner configuration. For example, such a configuration would be quite feasible to run:

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
> Read more about all available drivers on the [documentation](https://docs.roadrunner.dev/docs/queues-and-jobs/overview-queues) page.

After starting the server with this configuration, one driver named `local` will be available to you.

### Pushing Tasks

The following code pushes a task to the `local` pipeline via RPC:

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
$task = $queue->create('ping', '{"site": "https://example.com"}') // Create task with "ping" name
    ->withHeader('attempts', '4') // Number of attempts to execute the task
    ->withHeader('retry-delay', '10'); // Delay between attempts

// Push "ping" task to the queue
$task = $queue->dispatch($task);

var_dump($task->getId() . ' has been queued');
```

### Consuming Tasks

The consumer runs inside a RoadRunner worker (`consumer.php` from the configuration above), receives tasks and acknowledges them:

```php
<?php

use Spiral\RoadRunner\Jobs\Consumer;

require __DIR__ . '/vendor/autoload.php';

$consumer = new Consumer();

while ($task = $consumer->waitTask()) {
    try {
        $name = $task->getName(); // "ping"
        $queue = $task->getQueue(); // "local"
        $driver = $task->getDriver(); // Driver::Memory
        $payload = $task->getPayload(); // {"site": "https://example.com"}

        // Process task

        $task->ack();
    } catch (\Throwable $e) {
        $task->requeue($e);
    }
}
```

## Task Acknowledgement

The Consumer processes tasks from RoadRunner server and responds based on the processing outcome:

- `ack` - is used for positive acknowledgements.
- `nack` - is used for negative acknowledgements.
- `requeue` - is used for requeuing the task.

The behavior of the `nack` method depends on its implementation by the queue driver. It can accept an additional
parameter **redelivery**; if it is passed and set to **true**, the task will be requeued. However, not all drivers 
support this functionality. If the redelivery parameter is not passed, set to **false**, or the queue driver's
implementation does not support it, the task will not be requeued.

```php
$task->nack(message: $reason, redelivery: true);
```

The `requeue` method is implemented by RoadRunner and does not depend on the queue driver. It allows you to resend
the task to **the end of the queue** and add additional headers to the task.

```php
$task->withHeader('attempts', (string) ($attempts + 1))->requeue($exception);
```

The `nack` and `requeue` methods have the ability to specify a **delay** for requeuing the task. To do this, call 
the `withDelay` method and pass the desired value before invoking the `nack` or `requeue` methods.

```php
$task->withDelay(10)->requeue($exception);
```

<a href="https://spiral.dev/">
<img src="https://user-images.githubusercontent.com/773481/220979012-e67b74b5-3db1-41b7-bdb0-8a042587dedc.jpg" alt="try Spiral Framework" />
</a>
