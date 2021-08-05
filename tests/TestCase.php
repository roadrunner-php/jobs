<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Jobs\Tests\Stub\RPCConnectionStub;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param array<string, string|callable> $mapping
     * @return RPCInterface
     */
    protected function rpc(array $mapping = []): RPCInterface
    {
        return new RPCConnectionStub($mapping);
    }
}
