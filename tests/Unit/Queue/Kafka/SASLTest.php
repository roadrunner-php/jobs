<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Tests\Unit\Queue\Kafka;

use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\Jobs\Queue\Kafka\SASL;

final class SASLTest extends TestCase
{
    public function testBasic(): void
    {
        $sasl = SASL::basic('my-username', 'my-password', 'my-zid');

        $expected = [
            'mechanism' => 'plain',
            'username' => 'my-username',
            'password' => 'my-password',
            'zid' => 'my-zid',
        ];

        $this->assertEquals($expected, $sasl->jsonSerialize());
    }

    public function testBasicWithoutZid(): void
    {
        $sasl = SASL::basic('my-username', 'my-password');

        $expected = [
            'mechanism' => 'plain',
            'username' => 'my-username',
            'password' => 'my-password',
        ];

        $this->assertEquals($expected, $sasl->jsonSerialize());
    }

    public function testAWS(): void
    {
        $sasl = SASL::aws('my-access-key', 'my-secret-key', 'my-session-token', 'my-user-agent');

        $expected = [
            'mechanism' => 'aws_msk_iam',
            'access_key' => 'my-access-key',
            'secret_key' => 'my-secret-key',
            'session_token' => 'my-session-token',
            'user_agent' => 'my-user-agent',
        ];

        $this->assertEquals($expected, $sasl->jsonSerialize());
    }

    public function testAWSWithoutSessionTokenAndUserAgent(): void
    {
        $sasl = SASL::aws('my-access-key', 'my-secret-key');

        $expected = [
            'mechanism' => 'aws_msk_iam',
            'access_key' => 'my-access-key',
            'secret_key' => 'my-secret-key',
        ];

        $this->assertEquals($expected, $sasl->jsonSerialize());
    }
}