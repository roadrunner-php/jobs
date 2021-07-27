<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\QueueInterface;

/**
 * @psalm-suppress MissingImmutableAnnotation QueuedTask class is mutable.
 */
final class PreparedTask extends Task implements PreparedTaskInterface
{
    /**
     * @var OptionsInterface
     */
    private OptionsInterface $options;

    /**
     * @var QueueInterface
     */
    private QueueInterface $context;

    /**
     * @param QueueInterface $context
     * @param OptionsInterface $options
     * @param non-empty-string $name
     * @param array $payload
     */
    public function __construct(
        QueueInterface $context,
        OptionsInterface $options,
        string $name,
        array $payload
    ) {
        $this->context = $context;
        $this->options = clone $options;

        parent::__construct($this->context->getName(), $name, $payload);
    }

    /**
     * @return OptionsInterface
     */
    public function getOptions(): OptionsInterface
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(): QueuedTaskInterface
    {
        return $this->context->dispatch($this);
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function on(string $queue): self
    {
        assert($queue !== '', 'Precondition [queue !== ""] failed');

        $self = clone $this;
        $self->queue = $queue;

        return $self;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function with($value, $name = null): self
    {
        $name ??= $this->getPayloadNextIndex();
        assert(\is_string($name) || \is_int($name), 'Precondition [name is string|int] failed');

        $self = clone $this;
        $self->payload[$name] = $value;

        return $self;
    }

    /**
     * @return int
     */
    private function getPayloadNextIndex(): int
    {
        /** @var array<int> $indices */
        $indices = \array_filter(\array_keys($this->getPayload()), '\\is_int');

        if ($indices === []) {
            return 0;
        }

        return \max($indices) + 1;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function except($name): self
    {
        assert(\is_string($name) || \is_int($name), 'Precondition [name is string|int] failed');

        $self = clone $this;
        unset($self->payload[$name]);

        return $self;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function withHeader(string $name, $value): self
    {
        assert($name !== '', 'Precondition [name !== ""] failed');

        $value = \is_iterable($value) ? $value : [$value];

        $self = clone $this;
        $self->headers[$name] = [];

        foreach ($value as $item) {
            $self->headers[$name][] = $item;
        }

        return $self;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function withAddedHeader(string $name, $value): self
    {
        assert($name !== '', 'Precondition [name !== ""] failed');

        /** @var iterable<non-empty-string> $value */
        $value = \is_iterable($value) ? $value : [$value];

        /** @var array<non-empty-string> $headers */
        $headers = $this->headers[$name] ?? [];

        foreach ($value as $item) {
            $headers[] = $item;
        }

        return $this->withHeader($name, $headers);
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function withoutHeader(string $name): self
    {
        assert($name !== '', 'Precondition [name !== ""] failed');

        if (!isset($this->headers[$name])) {
            return $this;
        }

        $self = clone $this;
        unset($self->headers[$name]);
        return $self;
    }


    /**
     * {@inheritDoc}
     */
    public function getDelay(): int
    {
        return $this->options->getDelay();
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function await(int $seconds): self
    {
        assert($seconds >= 0, 'Precondition [seconds >= 0] failed');

        $self = clone $this;
        $self->options = Options::from($this->options)
            ->withDelay($seconds)
        ;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        return $this->options->getPriority();
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function prioritize(int $priority): self
    {
        assert($priority >= 0, 'Precondition [priority >= 0] failed');

        $self = clone $this;
        $self->options = Options::from($this->options)
            ->withPriority($priority)
        ;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttempts(): int
    {
        return $this->options->getAttempts();
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function retry(int $times): self
    {
        assert($times >= 0, 'Precondition [times >= 0] failed');

        $self = clone $this;
        $self->options = Options::from($this->options)
            ->withAttempts($times)
        ;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getRetryDelay(): int
    {
        return $this->options->getRetryDelay();
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function backoff(int $seconds): self
    {
        assert($seconds >= 0, 'Precondition [seconds >= 0] failed');

        $self = clone $this;
        $self->options = Options::from($this->options)
            ->withRetryDelay($seconds)
        ;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeout(): int
    {
        return $this->options->getTimeout();
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function timeout(int $seconds): self
    {
        assert($seconds >= 0, 'Precondition [seconds >= 0] failed');

        $self = clone $this;
        $self->options = Options::from($this->options)
            ->withTimeout($seconds)
        ;

        return $self;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->options = clone $this->options;
    }
}
