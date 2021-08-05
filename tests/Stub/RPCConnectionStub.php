<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Stub;

use Google\Protobuf\Internal\Message;
use Spiral\Goridge\RPC\Codec\JsonCodec;
use Spiral\Goridge\RPC\CodecInterface;
use Spiral\Goridge\RPC\RPCInterface;

final class RPCConnectionStub implements RPCInterface
{
    /**
     * @var CodecInterface
     */
    private CodecInterface $codec;

    /**
     * @var array<string, mixed>
     */
    private array $mapping;

    /**
     * @param array<string, mixed> $mapping
     */
    public function __construct(array $mapping = [])
    {
        $this->mapping = $mapping;
        $this->codec = new JsonCodec();
    }

    public function withServicePrefix(string $service): RPCInterface
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    public function withCodec(CodecInterface $codec): RPCInterface
    {
        $self = clone $this;
        $self->codec = $codec;
        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function call(string $method, $payload, $options = null)
    {
        if (!\array_key_exists($method, $this->mapping)) {
            throw new \BadFunctionCallException(
                \sprintf('RPC method [%s] has not been defined', $method)
            );
        }

        $result = $this->mapping[$method];

        if ($result instanceof \Closure) {
            $result = $result($payload);
        }

        if ($result instanceof Message) {
            $result = $result->serializeToString();
        }

        return $this->codec->decode((string)$result, $options);
    }
}
