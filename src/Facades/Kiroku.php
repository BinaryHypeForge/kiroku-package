<?php

namespace BinaryHype\Kiroku\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BinaryHype\Kiroku\Kiroku
 */
class Kiroku extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BinaryHype\Kiroku\Kiroku::class;
    }
}
