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

/**
 * Serializes job payloads.
 *
 * Please note that this implementation (including the interface) may change in
 * the future. Use a serializer implementation change only as a last resort.
 */
interface SerializerInterface
{
    /**
     * Serializes payload.
     *
     * @param array $payload
     *
     * @throws SerializationException
     *
     * @return string
     */
    public function serialize(array $payload): string;

    /**
     * Deserializes payload.
     *
     * @param string $payload
     *
     * @throws SerializationException
     *
     * @return array
     */
    public function deserialize(string $payload): array;
}
