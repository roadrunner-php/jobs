<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

interface TaskInterface
{
    /**
     * Returns the (non-empty) name of the task/job.
     *
     * @psalm-immutable
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * Returns payload of the task/job.
     *
     * @psalm-immutable
     * @return array
     */
    public function getPayload(): array;

    /**
     * Retrieves value from payload by its key.
     *
     * @psalm-immutable
     * @param array-key $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key, $default = null);

    /**
     * Determines that key defined in the payload.
     *
     * @psalm-immutable
     * @param array-key $key
     * @return bool
     */
    public function hasValue($key): bool;

    /**
     * Returns list of the headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     * <code>
     *  foreach ($task->getHeaders() as $name => $values) {
     *      echo $name . ': ' . implode(', ', $values) . "\n";
     *  }
     * </code>
     *
     * <code>
     *  // Example with output of all headers
     *  foreach ($task->getHeaders() as $name => $values) {
     *      foreach ($values as $value) {
     *          echo $name . ': ' . $value . "\n";
     *      }
     *  }
     * </code>
     *
     * @psalm-immutable
     * @return array<non-empty-string, array<string>>
     */
    public function getHeaders(): array;

    /**
     * Checks if a header exists by the given name.
     *
     * @psalm-immutable
     * @param non-empty-string $name Header field name.
     * @return bool Returns {@see true} if any header names match the given
     *              header name by string comparison. Returns {@see false} if
     *              no matching header name is found in the message.
     */
    public function hasHeader(string $name): bool;

    /**
     * Retrieves the task's header value by the given name.
     *
     * This method returns an array of all the header values of the given
     * header name.
     *
     * If the header does not appear in the task, this method MUST return an
     * empty array.
     *
     * @psalm-immutable
     * @param non-empty-string $name
     * @return array<string>
     */
    public function getHeader(string $name): array;

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all the header values of the given header name as a
     * string concatenated together using a comma (",").
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use {@see getHeader()} instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @psalm-immutable
     * @param non-empty-string $name
     * @return string
     */
    public function getHeaderLine(string $name): string;
}
