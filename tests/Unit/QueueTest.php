<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use RoadRunner\Jobs\DTO\V1\PushBatchRequest;
use RoadRunner\Jobs\DTO\V1\PushRequest;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\Queue;

class QueueTestCase extends TestCase
{
    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     * @return Queue
     */
    protected function queue(array $mapping = [], string $name = 'queue'): Queue
    {
        return new Queue($name, $this->rpc($mapping));
    }

    private function randomName(): string
    {
        return 'generated-' . \bin2hex(\random_bytes(32));
    }

    public function testName(): void
    {
        $queue = $this->queue([], $expect = $this->randomName());

        $this->assertSame($expect, $queue->getName());
    }

    public function testTaskDispatch(): void
    {
        $actual = null;
        $queue = $this->queue(['jobs.Push' => function(PushRequest $req) use (&$actual) {
            $job = $req->getJob();
            $actual = $job->getJob();
        }]);

        $queue->dispatch($queue->create(
            $expect = $this->randomName()
        ));

        $this->assertSame($expect, $actual);
    }

    public function testTaskDispatchUsingPushMethod(): void
    {
        $actual = null;
        $queue = $this->queue(['jobs.Push' => function(PushRequest $req) use (&$actual) {
            $job = $req->getJob();
            $actual = $job->getJob();
        }]);

        $queue->push($expect = $this->randomName());

        $this->assertSame($expect, $actual);
    }

    public function testMultipleTasksDispatch(): void
    {
        $expect = $actual = [];

        $queue = $this->queue(['jobs.PushBatch' => function(PushBatchRequest $req) use (&$actual) {
            foreach ($req->getJobs() as $job) {
                $actual[] = $job->getJob();
            }
        }]);

        $queue->dispatchMany(
            $queue->create($expect[] = $this->randomName()),
            $queue->create($expect[] = $this->randomName()),
            $queue->create($expect[] = $this->randomName()),
        );

        $this->assertSame($expect, $actual);
    }

    public function testPausing(): void
    {
        $paused = false;
        $handler = ['jobs.Pause' => static function () use (&$paused) {
            $paused = true;
        }];

        $this->queue($handler)
            ->pause()
        ;

        $this->assertTrue($paused);
    }

    public function testPausingError(): void
    {
        $this->expectException(JobsException::class);

        $queue = $this->queue();
        $queue->pause();
    }

    public function testResuming(): void
    {
        $resumed = false;
        $handler = ['jobs.Resume' => static function () use (&$resumed) {
            $resumed = true;
        }];

        $this->queue($handler)
            ->resume()
        ;

        $this->assertTrue($resumed);
    }

    public function testResumingError(): void
    {
        $this->expectException(JobsException::class);

        $queue = $this->queue();
        $queue->resume();
    }

    public function testCreateWithHeaders(): void
    {
        $queue = $this->queue();

        $this->assertSame(
            ['foo' => ['bar']],
            $queue->create(
                name: 'foo',
                payload: 'bar',
                options: (new Options())->withHeader('foo', 'bar')
            )
                ->getHeaders()
        );
    }

    public function testCreateWithoutHeaders(): void
    {
        $queue = $this->queue();

        $this->assertSame([], $queue->create('foo')->getHeaders());
    }
}
