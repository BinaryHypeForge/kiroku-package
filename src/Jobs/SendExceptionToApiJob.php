<?php

namespace BinaryHype\Kiroku\Jobs;

use BinaryHype\Kiroku\Actions\SendExceptionToAPI;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendExceptionToApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly Throwable $throwable
    )
    {
    }

    public function handle(): void
    {
        app()->instance('processing-kiroku-exception', true);

        try {
            (new SendExceptionToAPI())($this->throwable);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        } finally {
            app()->forgetInstance('processing-kiroku-exception');
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error($exception->getMessage());
    }
}