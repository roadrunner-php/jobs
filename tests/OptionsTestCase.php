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
