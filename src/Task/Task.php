<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

/**
 * @psalm-immutable
 * @psalm-allow-private-mutation
 */
abstract class Task implements TaskInterface
{
    use HeadersTrait;

    /**
     * @var non-empty-string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $payload;

    /**
     * @param non-empty-string $name
     * @param string $payload
     * @param array<non-empty-string, array<string>> $headers
     */
    public function __construct(string $name, string $payload, array $headers = [])
    {
        assert($name !== '', 'Precondition [job !== ""] failed');

        $this->name = $name;
        $this->payload = $payload;
        $this->headers = $headers;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
