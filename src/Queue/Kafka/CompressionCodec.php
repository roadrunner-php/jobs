<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

/**
 * The enum that represents the type of compression to use on messages.
 */
enum CompressionCodec: string
{
    case None = 'none';

    case Gzip = 'gzip';

    case Snappy = 'snappy';

    case Lz4 = 'lz4';

    case Zstd = 'zstd';
}
