<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Serializer;

use Spiral\RoadRunner\Jobs\Exception\SerializationException;

final class DefaultSerializer implements SerializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function serialize(array $payload): string
    {
        try {
            return \Opis\Closure\serialize($payload);
        } catch (\Throwable $e) {
            throw new SerializationException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserialize(string $payload): array
    {
        try {
            return (array)\Opis\Closure\unserialize($payload);
        } catch (\Throwable $e) {
            throw new SerializationException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
