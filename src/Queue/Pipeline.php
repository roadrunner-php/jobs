<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue;

use Ramsey\Uuid\Uuid;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Jobs\DTO\V1\HeaderValue;
use Spiral\RoadRunner\Jobs\DTO\V1\Job;
use Spiral\RoadRunner\Jobs\DTO\V1\Options as OptionsMessage;
use Spiral\RoadRunner\Jobs\DTO\V1\PushBatchRequest;
use Spiral\RoadRunner\Jobs\DTO\V1\PushRequest;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\OptionsAwareInterface;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\QueueInterface;
use Spiral\RoadRunner\Jobs\Task\PreparedTaskInterface;
use Spiral\RoadRunner\Jobs\Task\QueuedTask;
use Spiral\RoadRunner\Jobs\Task\QueuedTaskInterface;
use Spiral\RoadRunner\Jobs\Task\TaskInterface;

/**
 * @internal Executor is an internal library class, please do not use it in your code.
 * @psalm-internal Spiral\RoadRunner\Jobs
 */
final class Pipeline
{
    public function __construct(
        private readonly QueueInterface $queue,
        private readonly RPCInterface $rpc
    ) {
    }

    /**
     * @throws JobsException
     */
    public function send(PreparedTaskInterface $task): QueuedTaskInterface
    {
        try {
            $job = $this->taskToProto($task, $task);
            $this->rpc->call('jobs.Push', new PushRequest(['job' => $job]));
        } catch (JobsException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }

        return $this->createQueuedTask($job, $task);
    }

    /**
     * @param array<PreparedTaskInterface> $tasks
     * @return array<QueuedTaskInterface>
     * @throws JobsException
     */
    public function sendMany(array $tasks): array
    {
        try {
            $result = $jobs = [];

            foreach ($tasks as $task) {
                $job = $jobs[] = $this->taskToProto($task, $task);
                $result[] = $this->createQueuedTask($job, $task);
            }

            $this->rpc->call('jobs.PushBatch', new PushBatchRequest([
                'jobs' => $jobs
            ]));
        } catch (JobsException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new JobsException($e->getMessage(), (int)$e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @return non-empty-string
     */
    private function createTaskId(): string
    {
        return (string)Uuid::uuid4();
    }

    private function taskToProto(TaskInterface $task, OptionsInterface $options): Job
    {
        return new Job([
            'job' => $task->getName(),
            'id' => $this->createTaskId(),
            'payload' => $task->getPayload(),
            'headers' => $this->headersToProtoData($task),
            'options' => $this->optionsToProto($options),
        ]);
    }

    /**
     * @return array<string, HeaderValue>
     */
    private function headersToProtoData(TaskInterface $task): array
    {
        $result = [];

        foreach ($task->getHeaders() as $name => $values) {
            if (\count($values) === 0) {
                continue;
            }

            $result[$name] = new HeaderValue([
                'value' => $values,
            ]);
        }

        return $result;
    }

    private function optionsToProto(OptionsInterface $options): OptionsMessage
    {
        if ($options instanceof OptionsAwareInterface) {
            $options = $options->getOptions();
        }

        if (\method_exists($options, 'toArray')) {
            /** @var array $data */
            $data = $options->toArray();
        } else {
            $data = [
                'priority' => $options->getPriority(),
                'delay' => $options->getDelay(),
                'auto_ack' => $options->getAutoAck(),
            ];
        }

        return new OptionsMessage(
            \array_merge($data, ['pipeline' => $this->queue->getName()])
        );
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion Protobuf Job ID can not be empty
     */
    private function createQueuedTask(Job $job, TaskInterface $task): QueuedTask
    {
        return new QueuedTask(
            $job->getId(),
            $this->queue->getName(),
            $task->getName(),
            $task->getPayload(),
            $task->getHeaders()
        );
    }
}
