<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

/**
 * The enum that represents the type of compression to use on messages.
 *
 * @psalm-type CompressionCodecEnum = CompressionCodec::CODEC_*
 */
interface CompressionCodec
{
    /**
     * @var string
     * @psalm-var CompressionCodecEnum
     */
    public const CODEC_NONE = 'none';

    /**
     * @var string
     * @psalm-var CompressionCodecEnum
     */
    public const CODEC_GZIP = 'gzip';

    /**
     * @var string
     * @psalm-var CompressionCodecEnum
     */
    public const CODEC_SNAPPY = 'snappy';

    /**
     * @var string
     * @psalm-var CompressionCodecEnum
     */
    public const CODEC_LZ4 = 'lz4';

    /**
     * @var string
     * @psalm-var CompressionCodecEnum
     */
    public const CODEC_ZSTD = 'zstd';
}
