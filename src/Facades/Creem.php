<?php

namespace Creem\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Creem\Laravel\Creem
 */
class Creem extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'creem';
    }
}
