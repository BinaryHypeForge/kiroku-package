<?php

namespace BinaryHype\Kiroku\Actions;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendExceptionToAPI
{
    public function __invoke(\Throwable $exception): void
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('kiroku.api.bearer_token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post(config('kiroku.api.url'), [
                    // exception data
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                    'exception_class' => get_class($exception),

                    // request data
                    'request' => $this->getRequestData(),

                    // app data
                    'app' => config('app.name'),
                    'url' => config('app.url'),
                    'environment' => config('app.env'),
                    'timestamp' => now()->toDateTimeString(),
                ]);

            if ($response->unauthorized()) {
                $message = 'Unauthorized to send exception to API with code ' . $response->status();
                Log::error($message);
                throw new Exception($message);
            }

            if ($response->failed()) {
                $message = 'Failed to send exception to API with code ' . $response->status() . ': ' . $response->body();
                Log::error($message);
                throw new Exception($message);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send exception to API: ' . $e->getMessage());

            // Re-throw to trigger job retry mechanism
            throw $e;
        }
    }

    private function getRequestData(): ?array
    {
        if (!app()->bound('request')) {
            return null;
        }

        return [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'headers' => request()->headers->all(),
            'input' => request()->except(['password', 'password_confirmation', 'token', '_token']),
        ];
    }
}