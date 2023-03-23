<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

/**
 * @psalm-type TypeEnum = Type::*
 */
interface Type
{
    /**
     * @var TypeEnum
     */
    public const SUCCESS = 0;

    /**
     * @var TypeEnum
     */
    public const ERROR = 1;
}
