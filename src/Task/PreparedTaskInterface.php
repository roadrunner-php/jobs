<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\OptionsInterface;

interface PreparedTaskInterface extends TaskInterface, OptionsInterface
{
    /**
     * Adds additional data to the task.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated payload data.
     *
     * See {@see getPayload()} to retrieve information about the current value.
     *
     * @param mixed $value Passed payload data
     * @param array-key|null $name Optional payload data's name (key)
     * @return static
     */
    public function withValue($value, $name = null): self;

    /**
     * Excludes payload data from task by given key (name).
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated payload data.
     *
     * See {@see getPayload()} to retrieve information about the current value.
     *
     * @param array-key $name
     * @return static
     */
    public function withoutValue($name): self;

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * See also {@see getHeaders()}, {@see getHeader()} or {@see hasHeader()}
     * to retrieve information about the current value.
     *
     * @param non-empty-string $name Header field name.
     * @param non-empty-string|iterable<non-empty-string> $value Header value(s).
     * @return static
     */
    public function withHeader(string $name, $value): self;

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * See also {@see getHeaders()}, {@see getHeader()} or {@see hasHeader()}
     * to retrieve information about the current value.
     *
     * @param non-empty-string $name Header field name to add.
     * @param non-empty-string|iterable<non-empty-string> $value Header value(s).
     * @return static
     */
    public function withAddedHeader(string $name, $value): self;

    /**
     * Return an instance without the specified header.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * See also {@see getHeaders()}, {@see getHeader()} or {@see hasHeader()}
     * to retrieve information about the current value.
     *
     * @param non-empty-string $name Header field name to remove.
     * @return static
     */
    public function withoutHeader(string $name): self;

    /**
     * Specify the time to wait in seconds before executing the specified task.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new task delay option.
     *
     * See {@see getDelay()} to retrieve information about the current value.
     *
     * @param positive-int|0 $seconds
     * @return static
     */
    public function withDelay(int $seconds): self;
}
