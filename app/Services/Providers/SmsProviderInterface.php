<?php

namespace App\Services\Providers;

// every provider should implement this interface
interface SmsProviderInterface
{
    public function send(string $phone, string $message): SmsResult;
}
