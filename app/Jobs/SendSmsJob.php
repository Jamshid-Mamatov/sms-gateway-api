<?php

namespace App\Jobs;

use App\Models\SmsMessage;
use App\Services\Providers\ProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $tries = 3;
    public int $backoff = 60;

    public function __construct(private readonly SmsMessage $smsMessage)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $message = $this->smsMessage;

        $project = $message->project()->with('provider')->first();
        $provider = $project->provider;

        if(!$provider || !$provider->is_active) {
            $this->fail(new \RuntimeException("Provider [{$provider?->name}] is inactive or missing."));
            return;
        }

        try{

            $driver = ProviderFactory::create($provider->driver);

            $result = $driver->send($message->phone,$message->message);

            if($result->success) {
                $message->update([
                    'status'              => 'sent',
                    'provider_response'   => $result->raw,
                    'provider_message_id' => $result->messageId,
                    'sent_at'             => now(),
                ]);
            }
            else {

                if ($this->attempts() < $this->tries) {
                    throw new \RuntimeException($result->error ?? 'Provider returned failure');
                }

                $message->update([
                    'status'            => 'failed',
                    'provider_response' => array_merge($result->raw, ['error' => $result->error]),
                ]);
            }

        }catch (\Throwable $e) {
            Log::error('SendSmsJob failed', [
                'sms_message_id' => $message->id,
                'attempt'        => $this->attempts(),
                'error'          => $e->getMessage(),
            ]);

            // Re-throw so the queue can retry
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->smsMessage->update([
            'status'            => 'failed',
            'provider_response' => ['error' => $exception->getMessage()],
        ]);
    }
}
