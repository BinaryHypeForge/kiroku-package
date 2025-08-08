<?php

namespace BinaryHype\Kiroku;

use BinaryHype\Kiroku\Actions\SendExceptionToAPI;
use BinaryHype\Kiroku\Jobs\SendExceptionToApiJob;
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;

class Kiroku
{
    public static function logException(Throwable $ex): void
    {
        if (config('kiroku.queue.enabled')) {
            //        if (app()->runningInConsole() && app()->bound('processing-log-job')) {
            if (app()->bound('processing-log-job')) {
                return;
            }

            dispatch(new SendExceptionToApiJob($ex));
        } else {
            (new SendExceptionToAPI())($ex);
        }
    }

    public function handles(Exceptions $exceptions): void
    {
        $exceptions->reportable(static function (Throwable $exception) {
            self::logException($exception);
        });
    }
}
