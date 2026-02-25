<?php

namespace App\Services\Providers;

final class SmsResult
{

    public function __construct(
        public readonly bool   $success,
        public readonly array  $raw,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
    ) {}

    public static function success(array $raw, ?string $messageId = null): self
    {
        return new self(success: true, raw: $raw, messageId: $messageId);
    }

    public static function failure(string $error, array $raw = []): self
    {
        return new self(success: false, raw: $raw, error: $error);
    }
}
