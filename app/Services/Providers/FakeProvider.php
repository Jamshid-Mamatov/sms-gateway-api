<?php

namespace App\Services\Providers;

use Illuminate\Support\Str;

class FakeProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): SmsResult
    {
        // Simulate a small network delay (optional — remove in tests)
        // usleep(200_000);

        $messageId = 'FAKE-' . strtoupper(Str::random(12));

        return SmsResult::success(
            raw: [
                'provider' => 'fake',
                'phone' => $phone,
                'message' => $message,
                'message_id' => $messageId,
                'timestamp' => now()->toIso8601String(),
            ],
            messageId: $messageId,
        );
    }
}
