<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

interface TaskInterface extends ProvidesHeadersInterface
{
    /**
     * Returns the (non-empty) name of the task/job.
     *
     * @psalm-mutation-free
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * Returns payload of the task/job.
     *
     * @psalm-mutation-free
     * @return string
     */
    public function getPayload(): string;
}
