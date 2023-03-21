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
use Spiral\RoadRunner\Jobs\OptionsAwareInterface;
use Spiral\RoadRunner\Jobs\OptionsInterface;

/**
 * @psalm-suppress MissingImmutableAnnotation QueuedTask class is mutable.
 */
final class PreparedTask extends Task implements PreparedTaskInterface, OptionsAwareInterface
{
    use WritableHeadersTrait;

    /**
     * @var OptionsInterface
     */
    private OptionsInterface $options;

    /**
     * @param non-empty-string $name
     * @param string $payload
     * @param OptionsInterface|null $options
     * @param array<non-empty-string, array<string>> $headers
     */
    public function __construct(string $name, string $payload, OptionsInterface $options = null, array $headers = [])
    {
        $this->options = $options ?? new Options();

        parent::__construct($name, $payload, $headers);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->options = clone $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): OptionsInterface
    {
        return $this->options;
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
    public function withDelay(int $seconds): self
    {
        assert($seconds >= 0, 'Precondition [seconds >= 0] failed');

        if (!\method_exists($this->options, 'withDelay')) {
            return $this;
        }

        $self = clone $this;
        /** @psalm-suppress MixedAssignment */
        $self->options = $this->options->withDelay($seconds);

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
    public function withPriority(int $priority): self
    {
        assert($priority >= 0, 'Precondition [priority >= 0] failed');

        if (!\method_exists($this->options, 'withPriority')) {
            return $this;
        }

        $self = clone $this;
        /** @psalm-suppress MixedAssignment */
        $self->options = $this->options->withPriority($priority);

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoAck(): bool
    {
        return $this->options->getAutoAck();
    }

    /**
     * {@inheritDoc}
     */
    public function withAutoAck(bool $autoAck): self
    {
        if (!\method_exists($this->options, 'withAutoAck')) {
            return $this;
        }

        $self = clone $this;
        /** @psalm-suppress MixedAssignment */
        $self->options = $this->options->withAutoAck($autoAck);

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function withOptions(OptionsInterface $options): OptionsAwareInterface
    {
        $self = clone $this;
        $self->options = $options;

        return $self;
    }
}
