<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

interface ConsumerInterface
{
    /**
     * @return ReceivedTaskInterface|null
     */
    public function waitTask(): ?ReceivedTaskInterface;
}
