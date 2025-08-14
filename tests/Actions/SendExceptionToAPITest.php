<?php

use BinaryHype\Kiroku\Actions\SendExceptionToAPI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('posts exception to configured API with headers and request payload on success', function () {
    config()->set('kiroku.api.url', 'https://example.com/kiroku');
    config()->set('kiroku.api.bearer_token', 'test-token');

    // Bind a fake request so request data is present
    $req = request()->create('/test/path?foo=bar', 'POST', [
        'name' => 'john',
        'password' => 'secret',
        'token' => 'should_be_removed',
        '_token' => 'csrf',
    ], [], [], ['HTTP_USER_AGENT' => 'PestTest/1.0']);
    app()->instance('request', $req);

    Http::fake(function ($request) {
        expect($request->url())->toBe('https://example.com/kiroku')
            ->and($request->method())->toBe('POST')
            ->and($request->hasHeader('Authorization'))->toBeTrue()
            ->and($request->header('Authorization')[0])->toBe('Bearer test-token')
            ->and($request->header('Accept')[0])->toBe('application/json')
            ->and($request->header('Content-Type')[0])->toBe('application/json');

        $payload = $request->data();
        expect($payload)->toHaveKeys(['code', 'message', 'file', 'line', 'trace', 'exception_class', 'request', 'app', 'url', 'environment', 'timestamp'])
            ->and($payload['request'])->toBeArray()
            ->and($payload['request'])
            ->toHaveKeys(['url', 'method', 'ip', 'user_agent', 'headers', 'input'])
            ->and($payload['request']['input'])->not()->toHaveKeys(['password', 'token', '_token']);
        // Ensure sensitive fields were removed

        return Http::response(['ok' => true], 200);
    });

    (new SendExceptionToAPI)(new Exception('Test message', 123));

    Http::assertSentCount(1);
});

it('throws and logs when unauthorized', function () {
    config()->set('kiroku.api.url', 'https://example.com/kiroku');
    config()->set('kiroku.api.bearer_token', 'bad-token');

    Log::shouldReceive('error')->atLeast()->once();

    Http::fake(fn() => Http::response('Unauthorized', 401));

    $action = new SendExceptionToAPI;

    expect(fn() => $action(new Exception('Nope')))->toThrow(Exception::class);

    // Logging is invoked internally; we only assert an exception was thrown.
});

it('throws and logs when API fails', function () {
    config()->set('kiroku.api.url', 'https://example.com/kiroku');
    config()->set('kiroku.api.bearer_token', 'token');

    Log::shouldReceive('error')->atLeast()->once();

    Http::fake(fn() => Http::response('Server error', 500));

    $action = new SendExceptionToAPI;

    expect(fn() => $action(new Exception('Boom')))->toThrow(Exception::class);

    // Logging is invoked internally; we only assert an exception was thrown.
});
