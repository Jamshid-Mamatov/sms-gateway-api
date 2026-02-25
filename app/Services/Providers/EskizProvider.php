<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class EskizProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $login;
    private string $password;
    private string $from;
    private ?string $token = null;

    public function __construct(array $config)
    {
        $this->baseUrl  = rtrim($config['base_url'] ?? 'https://notify.eskiz.uz/api', '/');
        $this->login    = $config['login']    ?? '';
        $this->password = $config['password'] ?? '';
        $this->from     = $config['from']     ?? '4546';
    }

    public function send(string $phone, string $message): SmsResult
    {
        try {
            $token = $this->authenticate();

            // trim leading +
            $phoneClean = ltrim($phone, '+');

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/message/sms/send", [
                    'mobile_phone' => $phoneClean,
                    'message'      => $message,
                    'from'         => $this->from,
                    'callback_url' => '',
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? null) === 'waiting') {
                return SmsResult::success(
                    raw: $body,
                    messageId: (string) ($body['data']['id'] ?? null),
                );
            }

            return SmsResult::failure(
                error: $body['message'] ?? 'Unknown error from Eskiz',
                raw: $body,
            );
        } catch (\Throwable $e) {
            Log::error('EskizProvider error', ['error' => $e->getMessage(), 'phone' => $phone]);
            return SmsResult::failure(error: $e->getMessage());
        }
    }

    private function authenticate(): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = Http::timeout(10)
            ->post("{$this->baseUrl}/auth/login", [
                'email'    => $this->login,
                'password' => $this->password,
            ]);

        $body = $response->json();

        if (! $response->successful() || empty($body['data']['token'])) {
            throw new \RuntimeException('Eskiz authentication failed: ' . ($body['message'] ?? 'no token'));
        }

        $this->token = $body['data']['token'];
        return $this->token;
    }
}
