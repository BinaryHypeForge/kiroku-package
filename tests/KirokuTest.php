<?php

use BinaryHype\Kiroku\Jobs\SendExceptionToApiJob;
use BinaryHype\Kiroku\Kiroku;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

it('dispatches job when queue enabled', function () {
    config()->set('kiroku.queue.enabled', true);

    Queue::fake();

    Kiroku::logException(new Exception('Queued'));

    Queue::assertPushed(SendExceptionToApiJob::class, 1);
});

it('does not dispatch when processing-log-job is bound', function () {
    config()->set('kiroku.queue.enabled', true);

    Queue::fake();

    // According to current implementation, the key checked is 'processing-log-job'
    app()->instance('processing-log-job', true);

    Kiroku::logException(new Exception('Should be skipped'));

    Queue::assertNothingPushed();

    app()->forgetInstance('processing-log-job');
});

it('sends immediately via HTTP when queue disabled', function () {
    config()->set('kiroku.queue.enabled', false);
    config()->set('kiroku.api.url', 'https://example.com/kiroku');

    Http::fake(fn () => Http::response(['ok' => true], 200));

    Kiroku::logException(new Exception('Immediate'));

    Http::assertSentCount(1);
});
