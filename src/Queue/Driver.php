<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

/**
 * @psalm-type DriverType = Driver::*
 */
interface Driver
{
    /**
     * @psalm-var DriverType
     * @var string
     */
    public const EPHEMERAL = 'ephemeral';

    /**
     * @psalm-var DriverType
     * @var string
     */
    public const AMQP = 'amqp';

    /**
     * @psalm-var DriverType
     * @var string
     */
    public const BEANSTALK = 'beanstalk';

    /**
     * @psalm-var DriverType
     * @var string
     */
    public const SQS = 'sqs';

    /**
     * @internal Reserved for future use.
     *
     * @psalm-var DriverType
     * @var string
     */
    public const NAST = 'nast';

    /**
     * @internal Reserved for future use.
     *
     * @psalm-var DriverType
     * @var string
     */
    public const NSQ = 'nsq';
}
