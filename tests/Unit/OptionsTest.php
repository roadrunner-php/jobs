<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Spiral\RoadRunner\Jobs\Options;
use Testo\Assert;
use Testo\Test;

#[Test]
final class OptionsTest extends BaseTestCase
{
    public function testDelay(): void
    {
        $options = new Options(
            $expected = 0xDEAD_BEEF,
        );

        Assert::same($options->getDelay(), $expected);
    }

    public function testDelayImmutability(): void
    {
        $original = new Options(
            $expected = 0xDEAD_BEEF,
        );

        Assert::same($original->getDelay(), $expected);

        $mutable = $original->withDelay($expected * 2);

        Assert::same($original->getDelay(), $expected);
        Assert::same($mutable->getDelay(), $expected * 2);
    }

    public function testDelayCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                $delay = \random_int(0, \PHP_INT_MAX),
            ),
        );

        Assert::notSame($copy, $original);

        Assert::same($original->delay, $delay);
        Assert::same($copy->delay, $original->delay);
    }

    public function testDelayMergingWithDefaults(): void
    {
        $original = new Options(
            $delay = \random_int(0, \PHP_INT_MAX),
        );

        Assert::same($original->merge(new Options())->getDelay(), $delay);
        Assert::same((new Options())->merge($original)->getDelay(), $delay);
    }

    public function testDelayMergingByNewestValue(): void
    {
        $defaults = new Options(
            $delay = 0xDEAD_BEEF,
        );

        $modified = new Options(
            $delay * 2,
        );

        Assert::same($defaults->merge($modified)->getDelay(), $modified->getDelay());
    }

    public function testAutoAck(): void
    {
        $options = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $expected = true,
        );

        Assert::same($options->getAutoAck(), $expected);
    }

    public function testAutoAckImmutability(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $expected = true,
        );

        Assert::same($original->getAutoAck(), $expected);

        $mutable = $original->withAutoAck(false);

        Assert::same($original->getAutoAck(), true);
        Assert::same($mutable->getAutoAck(), false);
    }

    public function testAutoAckCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                Options::DEFAULT_DELAY,
                Options::DEFAULT_PRIORITY,
                $autoAck = true,
            ),
        );

        Assert::notSame($copy, $original);

        Assert::same($original->autoAck, $autoAck);
        Assert::same($copy->autoAck, $original->autoAck);
    }

    public function testAutoAckMergingWithDefaults(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            $autoAck = true,
        );

        Assert::same($original->merge(new Options())->getAutoAck(), $autoAck);
        Assert::same((new Options())->merge($original)->getAutoAck(), $autoAck);
    }

    public function testAutoAckMergingByNewestValue(): void
    {
        $defaults = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            false,
        );

        $modified = new Options(
            Options::DEFAULT_DELAY,
            Options::DEFAULT_PRIORITY,
            true,
        );

        Assert::same($defaults->merge($modified)->getAutoAck(), true);
    }

    public function testPriority(): void
    {
        $options = new Options(
            Options::DEFAULT_DELAY,
            $expected = 0xDEAD_BEEF,
        );

        Assert::same($options->getPriority(), $expected);
    }

    public function testPriorityImmutability(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            $expected = 0xDEAD_BEEF,
        );

        Assert::same($original->getPriority(), $expected);

        $mutable = $original->withPriority($expected * 2);

        Assert::same($original->getPriority(), $expected);
        Assert::same($mutable->getPriority(), $expected * 2);
    }

    public function testPriorityCreationFromAnotherOne(): void
    {
        $copy = Options::from(
            $original = new Options(
                Options::DEFAULT_DELAY,
                $priority = \random_int(0, \PHP_INT_MAX),
            ),
        );

        Assert::notSame($copy, $original);

        Assert::same($original->priority, $priority);
        Assert::same($copy->priority, $original->priority);
    }

    public function testPriorityMergingWithDefaults(): void
    {
        $original = new Options(
            Options::DEFAULT_DELAY,
            $priority = \random_int(0, \PHP_INT_MAX),
        );

        Assert::same($original->merge(new Options())->getPriority(), $priority);
        Assert::same((new Options())->merge($original)->getPriority(), $priority);
    }

    public function testPriorityMergingByNewestValue(): void
    {
        $defaults = new Options(
            Options::DEFAULT_DELAY,
            $priority = 0xDEAD_BEEF,
        );

        $modified = new Options(
            Options::DEFAULT_DELAY,
            $priority * 2,
        );

        Assert::same($defaults->merge($modified)->getPriority(), $modified->getPriority());
    }

    public function testMergingWithNull(): void
    {
        $expected = new Options();
        $actual = $expected->mergeOptional(null);

        Assert::same($actual, $expected);
    }

    public function testMergingWithNonNull(): void
    {
        $source = new Options(
            0xDEAD_BEEF,
        );

        $actual = $source->mergeOptional(
            $modified = new Options(
                0xDEAD_BEEF * 2,
            ),
        );

        // An "$actual" is new object
        Assert::notSame($source, $actual);
        Assert::notSame($modified, $actual);

        // Last options have been merged
        Assert::same($actual->getDelay(), $modified->getDelay());
    }
}
