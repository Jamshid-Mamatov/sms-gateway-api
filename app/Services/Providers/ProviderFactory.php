<?php

namespace App\Services\Providers;

class ProviderFactory
{
    public static function create(string $provider): SmsProviderInterface
    {
        return match ($provider) {
            'fake' => new FakeProvider(),
            'eskiz' => new EskizProvider($provider->config ?? []),
            default => throw new \InvalidArgumentException("Unknown provider: $provider"),
        };
    }
}
