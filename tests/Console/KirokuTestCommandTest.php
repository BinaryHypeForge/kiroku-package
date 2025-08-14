<?php

use Illuminate\Support\Facades\Http;

it('runs kiroku:test command and outputs success on 200 response', function () {
    config()->set('kiroku.api.url', 'https://example.com/kiroku');

    Http::fake(fn () => Http::response(['ok' => true], 200));

    $this->artisan('kiroku:test')
        ->expectsOutput('Sending test exception to Kiroku...')
        ->expectsOutput('Test exception sent successfully')
        ->assertOk();
});

it('runs kiroku:test command and shows error on failure', function () {
    config()->set('kiroku.api.url', 'https://example.com/kiroku');

    Http::fake(fn () => Http::response('nope', 500));

    $this->artisan('kiroku:test')
        ->expectsOutput('Sending test exception to Kiroku...')
        ->expectsOutputToContain('Failed to send exception to API')
        ->assertOk();
});
