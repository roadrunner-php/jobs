<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Jobs\Tests\Unit\Stub\RPCConnectionStub;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param array<string, string|callable> $mapping
     */
    protected function rpc(array $mapping = []): RPCInterface
    {
        return new RPCConnectionStub($mapping);
    }
}
