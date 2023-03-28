<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use RoadRunner\Jobs\DTO\V1\PushBatchRequest;
use RoadRunner\Jobs\DTO\V1\PushRequest;
use RoadRunner\Jobs\DTO\V1\Stat;
use RoadRunner\Jobs\DTO\V1\Stats;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Queue;

class QueueTestCase extends TestCase
{
    public function testName(): void
    {
        $queue = $this->queue([], $expect = $this->randomName());

        $this->assertSame($expect, $queue->getName());
    }

    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     * @return Queue
     */
    protected function queue(array $mapping = [], string $name = 'queue', ?OptionsInterface $options = null): Queue
    {
        return new Queue($name, $this->rpc($mapping), $options);
    }

    public function testDefaultOptions(): void
    {
        $queue = $this->queue();

        $this->assertEquals(new Options(), $queue->getDefaultOptions());
    }

    public function testCustomDefaultOptions(): void
    {
        $queue = $this->queue(options: $options = $this->createMock(OptionsInterface::class));

        $this->assertEquals($options, $queue->getDefaultOptions());
    }

    public function testOverridingCustomDefaultOptions(): void
    {
        $queue = $this->queue(options: $options = $this->createMock(OptionsInterface::class));

        $queue = $queue->withDefaultOptions($newOptions = $this->createMock(OptionsInterface::class));

        $this->assertNotSame($options, $queue->getDefaultOptions());
        $this->assertSame($newOptions, $queue->getDefaultOptions());
    }

    private function randomName(): string
    {
        return 'generated-' . \bin2hex(\random_bytes(32));
    }

    public function testTaskDispatch(): void
    {
        $actual = null;
        $queue = $this->queue([
            'jobs.Push' => function (PushRequest $req) use (&$actual) {
                $job = $req->getJob();
                $actual = $job->getJob();
            },
        ]);

        $queue->dispatch(
            $queue->create(
                $expect = $this->randomName(),
                'foo=bar',
            ),
        );

        $this->assertSame($expect, $actual);
    }

    public function testTaskDispatchUsingPushMethod(): void
    {
        $actual = null;
        $queue = $this->queue([
            'jobs.Push' => function (PushRequest $req) use (&$actual) {
                $job = $req->getJob();
                $actual = $job->getJob();
            },
        ]);

        $queue->push($expect = $this->randomName(), 'foo=bar');

        $this->assertSame($expect, $actual);
    }

    public function testMultipleTasksDispatch(): void
    {
        $expect = $actual = [];

        $queue = $this->queue([
            'jobs.PushBatch' => function (PushBatchRequest $req) use (&$actual) {
                foreach ($req->getJobs() as $job) {
                    $actual[] = $job->getJob();
                }
            },
        ]);

        $queue->dispatchMany(
            $queue->create($expect[] = $this->randomName(), 'foo=bar'),
            $queue->create($expect[] = $this->randomName(), 'foo=bar'),
            $queue->create($expect[] = $this->randomName(), 'foo=bar'),
        );

        $this->assertSame($expect, $actual);
    }

    public function testPausing(): void
    {
        $paused = false;
        $handler = [
            'jobs.Pause' => static function () use (&$paused) {
                $paused = true;
            },
        ];

        $this->queue($handler)
            ->pause();

        $this->assertTrue($paused);
    }

    public function testPausingError(): void
    {
        $this->expectException(JobsException::class);

        $queue = $this->queue();
        $queue->pause();
    }

    public function testIsPaused(): void
    {
        $handler = [
            'jobs.Stat' => static fn () => new Stats([
                'stats' => [
                    new Stat([
                        'pipeline' => 'queue',
                        'ready' => false,
                    ])
                ]
            ]),
        ];

        $this->assertTrue($this->queue($handler)->isPaused());
    }

    public function testIsNotPaused(): void
    {
        $handler = [
            'jobs.Stat' => static fn () => new Stats([
                'stats' => [
                    new Stat([
                        'pipeline' => 'queue',
                        'ready' => true,
                    ]),
                    new Stat([
                        'pipeline' => 'test',
                        'ready' => false,
                    ])
                ]
            ]),
        ];

        $this->assertFalse($this->queue($handler)->isPaused());
        $this->assertFalse($this->queue($handler, 'foo')->isPaused());
    }

    public function testResuming(): void
    {
        $resumed = false;
        $handler = [
            'jobs.Resume' => static function () use (&$resumed) {
                $resumed = true;
            },
        ];

        $this->queue($handler)
            ->resume();

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
                options: (new Options())->withHeader('foo', 'bar'),
            )
                ->getHeaders(),
        );
    }

    public function testCreateWithoutHeaders(): void
    {
        $queue = $this->queue();

        $this->assertSame([], $queue->create('foo', 'foo=bar')->getHeaders());
    }
}
