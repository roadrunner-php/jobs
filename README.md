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

You can use the convenient installer to download the latest available compatible
version of RoadRunner assembly:

```bash
composer require spiral/roadrunner-cli --dev
vendor/bin/rr get
```

## Usage

First you need to add at least one jobs adapter to your RoadRunner configuration.
For example, such a configuration would be quite feasible to run:

```yaml
#
# RPC is required for tasks dispatching (client)
#
rpc:
    listen: tcp://127.0.0.1:6001

#
# This section configures the task consumer (server)
#
server:
    command: php consumer.php
    relay: pipes

#
# In this section, the jobs themselves are configured
#
jobs:
    consume: [ "test" ]   # List of RoadRunner queues that can be processed by 
                          # the consumer specified in the "server" section.
    pipelines:
        test:               # RoadRunner queue identifier
            driver: memory  # - Queue driver name
            queue: test       # - Internal (driver's) queue identifier
```

> **Note**
> Read more about all available drivers on the
> [documentation](https://roadrunner.dev/docs/beep-beep-jobs) page.

After starting the server with this configuration, one driver named "`test`"
will be available to you.

The following code will allow writing and reading an arbitrary value from the
RoadRunner server.

```php
<?php

use Spiral\RoadRunner\Jobs\Jobs;

require __DIR__ . '/vendor/autoload.php';

// Jobs service
$jobs = new Jobs(RPC::create('tcp://127.0.0.1:6001'));

// Select "test" queue from jobs
$queue = $jobs->connect('test');

// Create task prototype with default headers
$prototype = $queue->create('echo')
    ->withHeader('attempts', 4)
    ->withHeader('retry-delay', 10)
;

// Execute "echo" task with Closure as payload
$task = $queue->dispatch(
    $prototype->withValue(static fn($arg) => print $arg)
);

var_dump($task->getId() . ' has been queued');
```

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
