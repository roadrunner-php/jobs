<?php

declare(strict_types=1);

namespace Spiral\RoadRunner\Jobs\Queue\Kafka;

final class SASL implements \JsonSerializable
{
    private function __construct(
        public readonly array $payload,
    ) {
    }

    /**
     * @param non-empty-string $username Username to use for authentication. Required for the plain auth mechanism.
     * @param non-empty-string $password Password to use for authentication. Required for the plain auth mechanism.
     * @param non-empty-string|null $zid Zid is an optional authorization ID to use in authenticating.
     */
    public static function basic(string $username, string $password, ?string $zid = null): self
    {
        $payload = [
            'mechanism' => 'plain',
            'username' => $username,
            'password' => $password,
        ];

        if ($zid !== null) {
            $payload['zid'] = $zid;
        }

        return new self($payload);
    }

    public static function aws(
        string $accessKey,
        string $secretKey,
        ?string $sessionToken = null,
        ?string $userAgent = null,
    ): self {
        $payload = [
            'mechanism' => 'aws_msk_iam',
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
        ];

        if ($sessionToken !== null) {
            $payload['session_token'] = $sessionToken;
        }

        if ($userAgent !== null) {
            $payload['user_agent'] = $userAgent;
        }

        return new self($payload);
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}