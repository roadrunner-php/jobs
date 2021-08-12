<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

final class Options implements OptionsInterface
{
    /**
     * @var positive-int|0
     */
    public int $delay = self::DEFAULT_DELAY;

    /**
     * @param positive-int|0 $delay
     */
    public function __construct(
        int $delay = self::DEFAULT_DELAY
    ) {
        assert($delay >= 0, 'Precondition [delay >= 0] failed');

        $this->delay = $delay;
    }

    /**
     * @param OptionsInterface $options
     * @return static
     */
    public static function from(OptionsInterface $options): self
    {
        return new self(
            $options->getDelay()
        );
    }

    /**
     * @return positive-int|0
     */
    public function getDelay(): int
    {
        assert($this->delay >= 0, 'Invariant [delay >= 0] failed');

        return $this->delay;
    }

    /**
     * @psalm-immutable
     * @param positive-int|0 $delay
     * @return $this
     */
    public function withDelay(int $delay): self
    {
        assert($delay >= 0, 'Precondition [delay >= 0] failed');

        $self = clone $this;
        $self->delay = $delay;

        return $self;
    }

    /**
     * @param OptionsInterface|null $options
     * @return OptionsInterface
     */
    public function mergeOptional(?OptionsInterface $options): OptionsInterface
    {
        if ($options === null) {
            return $this;
        }

        return $this->merge($options);
    }

    /**
     * @param OptionsInterface $options
     * @return OptionsInterface
     */
    public function merge(OptionsInterface $options): OptionsInterface
    {
        $self = clone $this;

        if (($delay = $options->getDelay()) !== self::DEFAULT_DELAY) {
            $self->delay = $delay;
        }

        return $self;
    }
}
