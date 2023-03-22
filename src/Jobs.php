<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\Goridge\RPC\Codec\ProtobufCodec;
use Spiral\Goridge\RPC\RPC;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Jobs\DTO\V1\DeclareRequest;
use Spiral\RoadRunner\Jobs\DTO\V1\Pipelines;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\Queue\CreateInfoInterface;

final class Jobs implements JobsInterface
{
    private readonly RPCInterface $rpc;

    public function __construct(RPCInterface $rpc = null)
    {
        $this->rpc = ($rpc ?? $this->createRPCConnection())
            ->withCodec(new ProtobufCodec());
    }

    public function create(CreateInfoInterface $info): QueueInterface
    {
        try {
            $this->rpc->call('jobs.Declare', new DeclareRequest([
                'pipeline' => $this->toStringOfStringMap($info->toArray()),
            ]));

            return $this->connect($info->getName(), OptionsFactory::create($info->getDriver()));
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @param array<string, mixed> $map
     * @return array<string, string>
     * @throws \Throwable
     * @psalm-suppress MixedAssignment
     */
    private function toStringOfStringMap(array $map): array
    {
        $marshalled = [];

        foreach ($map as $key => $value) {
            $marshalled[$key] = match (true) {
                \is_int($value),
                \is_string($value),
                    $value instanceof \Stringable,
                    \is_object($value) && \method_exists($value, '__toString') => (string) $value,
                \is_bool($value) => $value ? 'true' : 'false',
                \is_array($value) => \json_encode($value, \JSON_THROW_ON_ERROR),
                default => throw new \InvalidArgumentException(
                    \sprintf('Can not cast to string unrecognized value of type %s', \get_debug_type($value))
                ),
            };
        }

        return $marshalled;
    }

    /**
     * @param non-empty-string $queue
     */
    public function connect(string $queue, ?OptionsInterface $options = null): QueueInterface
    {
        \assert($queue !== '', 'Precondition [queue !== ""] failed');

        return new Queue($queue, $this->rpc, $options);
    }

    public function pause(string|QueueInterface $queue, string|QueueInterface ...$queues): void
    {
        try {
            $this->rpc->call('jobs.Pause', new Pipelines([
                'pipelines' => $this->names($queue, ...$queues),
            ]));
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function resume(QueueInterface|string $queue, QueueInterface|string ...$queues): void
    {
        try {
            $this->rpc->call('jobs.Resume', new Pipelines([
                'pipelines' => $this->names($queue, ...$queues),
            ]));
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @throws JobsException
     */
    public function getIterator(): \Traversable
    {
        try {
            /** @var Pipelines $result */
            $result = $this->rpc->call('jobs.List', '', Pipelines::class);

            /** @psalm-var non-empty-string $queue */
            foreach ($result->getPipelines() as $queue) {
                yield $queue => $this->connect($queue);
            }
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @throws JobsException
     */
    public function count(): int
    {
        return \iterator_count($this->getIterator());
    }

    private function createRPCConnection(): RPCInterface
    {
        $env = Environment::fromGlobals();

        return RPC::create($env->getRPCAddress());
    }

    /**
     * @param QueueInterface|non-empty-string ...$queues
     * @return array<non-empty-string>
     */
    private function names(QueueInterface|string ...$queues): array
    {
        $names = [];

        foreach ($queues as $queue) {
            \assert(
                $queue instanceof QueueInterface || \is_string($queue),
                'Queue should be an instance of ' . QueueInterface::class .
                ' or type of string, but ' . \get_debug_type($queue) . ' passed'
            );

            if ($queue instanceof QueueInterface) {
                $queue = $queue->getName();
            }

            $names[] = $queue;
        }

        return $names;
    }
}
