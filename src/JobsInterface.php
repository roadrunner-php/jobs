<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\Queue\CreateInfoInterface;

/**
 * @template-extends \IteratorAggregate<string, QueueInterface>
 */
interface JobsInterface extends \IteratorAggregate, \Countable
{
    /**
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * @param non-empty-string $queue
     * @return QueueInterface
     */
    public function connect(string $queue): QueueInterface;

    /**
     * @param CreateInfoInterface $info
     * @return QueueInterface
     * @throws JobsException
     */
    public function create(CreateInfoInterface $info): QueueInterface;

    /**
     * @param QueueInterface|non-empty-string $queue
     * @param QueueInterface|non-empty-string ...$queues
     * @throws JobsException
     */
    public function pause($queue, ...$queues): void;

    /**
     * @param QueueInterface|non-empty-string $queue
     * @param QueueInterface|non-empty-string ...$queues
     * @throws JobsException
     */
    public function resume($queue, ...$queues): void;
}
