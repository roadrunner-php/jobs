<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit;

use Testo\Test;
use Testo\Assert;
use Testo\Expect;
use RoadRunner\Jobs\DTO\V1\PushBatchRequest;
use RoadRunner\Jobs\DTO\V1\PushRequest;
use RoadRunner\Jobs\DTO\V1\Stat;
use RoadRunner\Jobs\DTO\V1\Stats;
use Spiral\RoadRunner\Jobs\Exception\JobsException;
use Spiral\RoadRunner\Jobs\Options;
use Spiral\RoadRunner\Jobs\OptionsInterface;
use Spiral\RoadRunner\Jobs\Queue;

class QueueTestCase extends BaseTestCase
{
    #[Test]
    public function testName(): void
    {
        $queue = $this->queue([], $expect = $this->randomName());

        Assert::same($queue->getName(), $expect);
    }

    #[Test]
    public function testDefaultOptions(): void
    {
        $queue = $this->queue();

        Assert::equals($queue->getDefaultOptions(), new Options());
    }

    #[Test]
    public function testCustomDefaultOptions(): void
    {
        $queue = $this->queue(options: $options = \Mockery::mock(OptionsInterface::class)->shouldIgnoreMissing());

        Assert::equals($queue->getDefaultOptions(), $options);
    }

    #[Test]
    public function testOverridingCustomDefaultOptions(): void
    {
        $queue = $this->queue(options: $options = \Mockery::mock(OptionsInterface::class)->shouldIgnoreMissing());

        $queue = $queue->withDefaultOptions($newOptions = \Mockery::mock(OptionsInterface::class)->shouldIgnoreMissing());

        Assert::notSame($queue->getDefaultOptions(), $options);
        Assert::same($queue->getDefaultOptions(), $newOptions);
    }

    #[Test]
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

        Assert::same($actual, $expect);
    }

    #[Test]
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

        Assert::same($actual, $expect);
    }

    #[Test]
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

        Assert::same($actual, $expect);
    }

    #[Test]
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

        Assert::true($paused);
    }

    #[Test]
    public function testPausingError(): void
    {
        Expect::exception(JobsException::class);

        $queue = $this->queue();
        $queue->pause();
    }

    #[Test]
    public function testIsPaused(): void
    {
        $handler = [
            'jobs.Stat' => static fn() => new Stats([
                'stats' => [
                    new Stat([
                        'pipeline' => 'queue',
                        'ready' => false,
                    ]),
                ],
            ]),
        ];

        Assert::true($this->queue($handler)->isPaused());
    }

    #[Test]
    public function testIsNotPaused(): void
    {
        $handler = [
            'jobs.Stat' => static fn() => new Stats([
                'stats' => [
                    new Stat([
                        'pipeline' => 'queue',
                        'ready' => true,
                    ]),
                    new Stat([
                        'pipeline' => 'test',
                        'ready' => false,
                    ]),
                ],
            ]),
        ];

        Assert::false($this->queue($handler)->isPaused());
        Assert::false($this->queue($handler, 'foo')->isPaused());
    }

    #[Test]
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

        Assert::true($resumed);
    }

    #[Test]
    public function testResumingError(): void
    {
        Expect::exception(JobsException::class);

        $queue = $this->queue();
        $queue->resume();
    }

    #[Test]
    public function testCreateWithHeaders(): void
    {
        $queue = $this->queue();

        Assert::same($queue->create(
            name: 'foo',
            payload: 'bar',
            options: (new Options())->withHeader('foo', 'bar'),
        )
            ->getHeaders(), ['foo' => ['bar']]);
    }

    #[Test]
    public function testCreateWithoutHeaders(): void
    {
        $queue = $this->queue();

        Assert::same($queue->create('foo', 'foo=bar')->getHeaders(), []);
    }

    /**
     * @param array<string, string|callable> $mapping
     * @param non-empty-string $name
     */
    protected function queue(array $mapping = [], string $name = 'queue', ?OptionsInterface $options = null): Queue
    {
        return new Queue($name, $this->rpc($mapping), $options);
    }

    private function randomName(): string
    {
        return 'generated-' . \bin2hex(\random_bytes(32));
    }
}
