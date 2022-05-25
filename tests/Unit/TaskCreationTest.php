<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\Queue;
use Spiral\RoadRunner\Jobs\QueueInterface;

class TaskCreationTestCase extends TestCase
{
    public function testTaskCreation(): void
    {
        $expected = 'task-name-' . \bin2hex(\random_bytes(32));

        $task = $this->queue()
            ->create($expected);

        $this->assertSame($expected, $task->getName());
    }

    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     * @return QueueInterface
     */
    protected function queue(array $mapping = [], string $name = 'queue'): QueueInterface
    {
        return new Queue($name, $this->rpc($mapping));
    }

    public function testTaskCreationWithPayload(): void
    {
        $expected = ['a' => \random_int(0, \PHP_INT_MAX), 'b' => \random_bytes(32)];

        $task = $this->queue()
            ->create('task', $expected);

        $this->assertSame($expected, $task->getPayload());
    }
}
