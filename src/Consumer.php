<?php

/**
 * This file is part of RoadRunner package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs;

use Spiral\RoadRunner\Jobs\Exception\ReceivedTaskException;
use Spiral\RoadRunner\Jobs\Exception\SerializationException;
use Spiral\RoadRunner\Jobs\Serializer\DefaultSerializer;
use Spiral\RoadRunner\Jobs\Serializer\SerializerAwareInterface;
use Spiral\RoadRunner\Jobs\Serializer\SerializerInterface;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactory;
use Spiral\RoadRunner\Jobs\Task\Factory\ReceivedTaskFactoryInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\WorkerInterface;

/**
 * Note that the implementation of this class depends on the environment setting
 * of the RoadRunner. In the case that the current worker does NOT meet the
 * queue processing tasks, then the Consumer class is not guaranteed to work.
 *
 * In most cases, it will be enough for you to check the availability of the
 * environment parameter:
 *
 * <code>
 *  use Spiral\RoadRunner\Environment;
 *  use Spiral\RoadRunner\Environment\Mode;
 *
 *  $env = Environment::fromGlobals();
 *  if ($env->getMode() !== Mode::MODE_JOBS) {
 *     throw new RuntimeException('Can not create Jobs Consumer');
 *  }
 *
 *  $consumer = new Consumer(...);
 * </code>
 */
final class Consumer implements ConsumerInterface, SerializerAwareInterface
{
    private WorkerInterface $worker;
    private SerializerInterface $serializer;
    private ReceivedTaskFactoryInterface $receivedTaskFactory;

    public function __construct(
        WorkerInterface $worker = null,
        SerializerInterface $serializer = null,
        ReceivedTaskFactoryInterface $receivedTaskFactory = null
    ) {
        $this->worker = $worker ?? Worker::create();
        $this->serializer = $serializer ?? new DefaultSerializer();
        $this->receivedTaskFactory = $receivedTaskFactory ?? new ReceivedTaskFactory($this->serializer, $this->worker);
    }

    /**
     * {@inheritDoc}
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function withSerializer(SerializerInterface $serializer): SerializerAwareInterface
    {
        $self = clone $this;
        $self->serializer = $serializer;

        if ($self->receivedTaskFactory instanceof SerializerAwareInterface) {
            $self->receivedTaskFactory = $self->receivedTaskFactory->withSerializer($serializer);
        }

        return $self;
    }

    /**
     * @return ReceivedTaskInterface|null
     * @throws SerializationException
     * @throws ReceivedTaskException
     */
    public function waitTask(): ?ReceivedTaskInterface
    {
        $payload = $this->worker->waitPayload();

        if ($payload === null) {
            return null;
        }

        return $this->receivedTaskFactory->create($payload);
    }
}
