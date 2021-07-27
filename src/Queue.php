<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\Goridge\RPC\Codec\ProtobufCodec;
use Spiral\Goridge\RPC\RPC;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Jobs\Queue\Pipeline;
use Spiral\RoadRunner\Jobs\Serializer\DefaultSerializer;
use Spiral\RoadRunner\Jobs\Serializer\JsonSerializer;
use Spiral\RoadRunner\Jobs\Serializer\SerializerAwareInterface;
use Spiral\RoadRunner\Jobs\Serializer\SerializerInterface;
use Spiral\RoadRunner\Jobs\Task\PreparedTask;
use Spiral\RoadRunner\Jobs\Task\PreparedTaskInterface;
use Spiral\RoadRunner\Jobs\Task\QueuedTaskInterface;

final class Queue implements QueueInterface, SerializerAwareInterface
{
    /**
     * @var Options
     */
    private Options $options;

    /**
     * @var non-empty-string
     */
    private string $name;

    /**
     * @var Pipeline
     */
    private Pipeline $pipeline;

    /**
     * @param non-empty-string $name
     * @param RPCInterface|null $rpc
     * @param SerializerInterface|null $serializer
     */
    public function __construct(string $name, RPCInterface $rpc = null, SerializerInterface $serializer = null)
    {
        assert($name !== '', 'Precondition [name !== ""] failed');

        $rpc ??= $this->createRPCConnection();

        $this->pipeline = new Pipeline(
            $rpc->withCodec(new ProtobufCodec()),
            $serializer ?? new DefaultSerializer()
        );

        $this->name = $name;
        $this->options = new Options();
    }

    /**
     * @return RPCInterface
     */
    private function createRPCConnection(): RPCInterface
    {
        $env = Environment::fromGlobals();

        return RPC::create($env->getRPCAddress());
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->pipeline->getSerializer();
    }

    /**
     * {@inheritDoc}
     */
    public function withSerializer(SerializerInterface $serializer): SerializerAwareInterface
    {
        $self = clone $this;
        $self->pipeline = $this->pipeline->withSerializer($serializer);

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(): OptionsInterface
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function withDefaultOptions(?OptionsInterface $options): self
    {
        $self = clone $this;
        /** @psalm-suppress PropertyTypeCoercion */
        $self->options = $options ?? new Options();

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $name, array $payload = []): PreparedTaskInterface
    {
        return new PreparedTask($this, $this->options, $name, $payload);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(PreparedTaskInterface $task): QueuedTaskInterface
    {
        return $this->pipeline->send($task);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchMany(PreparedTaskInterface ...$tasks): iterable
    {
        return $this->pipeline->sendMany($tasks);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->options = clone $this->options;
    }
}
