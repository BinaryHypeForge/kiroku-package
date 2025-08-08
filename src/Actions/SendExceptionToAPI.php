<?php

namespace BinaryHype\Kiroku\Actions;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendExceptionToAPI
{
    public function __invoke(\Throwable $exception)
    {
        try {
            $response = Http::timeout(10)
                ->retry(2, 5000)
                ->post(config('kiroku.api.url'), [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                    'app' => config('app.name'),
                    'url' => config('app.url'),
                    'environment' => config('app.env'),
                    'timestamp' => now()->toDateTimeString(),
                ]);

            if ($response->failed()) {
                throw new Exception('Failed to send exception to API with code ' . $response->status() . ': ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send exception to API: ' . $e->getMessage());

            // Re-throw to trigger job retry mechanism
            throw $e;
        }
    }
}