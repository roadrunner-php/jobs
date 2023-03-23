<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Task;

/**
 * @mixin ProvidesHeadersInterface
 * @psalm-require-implements ProvidesHeadersInterface
 * @psalm-immutable
 */
trait HeadersTrait
{
    /**
     * @var array<non-empty-string, array<string>>
     */
    protected array $headers = [];

    /**
     * @psalm-return array<non-empty-string, array<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @psalm-param non-empty-string $name Header field name.
     * @psalm-return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]) && \count($this->headers[$name]) > 0;
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-return array<string>
     */
    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-return string
     */
    public function getHeaderLine(string $name): string
    {
        return \implode(',', $this->getHeader($name));
    }
}
