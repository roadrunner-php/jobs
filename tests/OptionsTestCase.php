<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests;

use Spiral\RoadRunner\Jobs\Options;

class OptionsTestCase extends TestCase
{
    public function testDelay(): void
    {
        $options = new Options(
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $options->getDelay());
    }

    public function testDelayImmutability(): void
    {
        $original = new Options(
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $original->getDelay());

        $mutable = $original->withDelay($expected * 2);

        $this->assertSame($expected, $original->getDelay());
        $this->assertSame($expected * 2, $mutable->getDelay());
    }

    public function testDelayCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                $delay = \random_int(0, \PHP_INT_MAX)
            )
        );

        $this->assertNotSame($original, $copy);

        $this->assertSame($delay, $original->delay);
        $this->assertSame($original->delay, $copy->delay);
    }

    public function testDelayMergingWithDefaults(): void
    {
        $original = new Options(
            $delay = \random_int(0, \PHP_INT_MAX)
        );

        $this->assertSame($delay, $original->merge(new Options())->getDelay());
        $this->assertSame($delay, (new Options())->merge($original)->getDelay());
    }

    public function testDelayMergingByNewestValue(): void
    {
        $defaults = new Options(
            $delay = 0xDEAD_BEEF
        );

        $modified = new Options(
            $delay * 2,
        );

        $this->assertSame($modified->getDelay(), $defaults->merge($modified)->getDelay());
    }

    public function testPriority(): void
    {
        $options = new Options(
            Options::DEFAULT_DELAY,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $options->getPriority());
    }

    public function testPriorityImmutability(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $original->getPriority());

        $mutable = $original->withPriority($expected * 2);

        $this->assertSame($expected, $original->getPriority());
        $this->assertSame($expected * 2, $mutable->getPriority());
    }

    public function testPriorityCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                Options::DEFAULT_DELAY,
                $priority = \random_int(0, \PHP_INT_MAX)
            )
        );

        $this->assertNotSame($original, $copy);

        $this->assertSame($priority, $original->priority);
        $this->assertSame($original->priority, $copy->priority);
    }

    public function testPriorityMergingWithDefaults(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            $priority = \random_int(0, \PHP_INT_MAX)
        );

        $this->assertSame($priority, $original->merge(new Options())->getPriority());
        $this->assertSame($priority, (new Options())->merge($original)->getPriority());
    }

    public function testPriorityMergingByNewestValue(): void
    {
        $defaults = new Options(
            Options::DEFAULT_DELAY,
            $priority = 0xDEAD_BEEF
        );

        $modified = new Options(
            Options::DEFAULT_DELAY,
            $priority * 2,
        );

        $this->assertSame($modified->getPriority(), $defaults->merge($modified)->getPriority());
    }

    public function testAttempts(): void
    {
        $options = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $options->getAttempts());
    }

    public function testAttemptsImmutability(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $original->getAttempts());

        $mutable = $original->withAttempts($expected * 2);

        $this->assertSame($expected, $original->getAttempts());
        $this->assertSame($expected * 2, $mutable->getAttempts());
    }

    public function testAttemptsCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                Options::DEFAULT_DELAY,
                Options::DEFAULT_PRIORITY,
                $attempts = \random_int(0, \PHP_INT_MAX)
            )
        );

        $this->assertNotSame($original, $copy);

        $this->assertSame($attempts, $original->attempts);
        $this->assertSame($original->attempts, $copy->attempts);
    }

    public function testAttemptsMergingWithDefaults(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $attempts = \random_int(0, \PHP_INT_MAX)
        );

        $this->assertSame($attempts, $original->merge(new Options())->getAttempts());
        $this->assertSame($attempts, (new Options())->merge($original)->getAttempts());
    }

    public function testAttemptsMergingByNewestValue(): void
    {
        $defaults = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $attempts = 0xDEAD_BEEF
        );

        $modified = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $attempts * 2,
        );

        $this->assertSame($modified->getAttempts(), $defaults->merge($modified)->getAttempts());
    }

    public function testRetryDelay(): void
    {
        $options = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            Options::DEFAULT_ATTEMPTS,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $options->getRetryDelay());
    }

    public function testRetryDelayImmutability(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            Options::DEFAULT_ATTEMPTS,
            $expected = 0xDEAD_BEEF
        );

        $this->assertSame($expected, $original->getRetryDelay());

        $mutable = $original->withRetryDelay($expected * 2);

        $this->assertSame($expected, $original->getRetryDelay());
        $this->assertSame($expected * 2, $mutable->getRetryDelay());
    }

    public function testRetryDelayCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                Options::DEFAULT_DELAY,
                Options::DEFAULT_PRIORITY,
                Options::DEFAULT_ATTEMPTS,
                $retryDelay = \random_int(0, \PHP_INT_MAX)
            )
        );

        $this->assertNotSame($original, $copy);

        $this->assertSame($retryDelay, $original->retryDelay);
        $this->assertSame($original->retryDelay, $copy->retryDelay);
    }

    public function testRetryDelayMergingWithDefaults(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            Options::DEFAULT_ATTEMPTS,
            $retryDelay = \random_int(0, \PHP_INT_MAX)
        );

        $this->assertSame($retryDelay, $original->merge(new Options())->getRetryDelay());
        $this->assertSame($retryDelay, (new Options())->merge($original)->getRetryDelay());
    }

    public function testRetryDelayMergingByNewestValue(): void
    {
        $defaults = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            Options::DEFAULT_ATTEMPTS,
            $retryDelay = 0xDEAD_BEEF
        );

        $modified = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            Options::DEFAULT_ATTEMPTS,
            $retryDelay * 2,
        );

        $this->assertSame($modified->getRetryDelay(), $defaults->merge($modified)->getRetryDelay());
    }

    public function testMergingWithNull(): void
    {
        $expected = new Options();
        $actual = $expected->mergeOptional(null);

        $this->assertSame($expected, $actual);
    }

    public function testMergingWithNonNull(): void
    {
        $source = new Options(
            0xDEAD_BEEF
        );

        $actual = $source->mergeOptional(
            $modified = new Options(
            0xDEAD_BEEF * 2
            )
        );

        // An "$actual" is new object
        $this->assertNotSame($actual, $source);
        $this->assertNotSame($actual, $modified);

        // Last options have been merged
        $this->assertSame($modified->getDelay(), $actual->getDelay());
    }
}
