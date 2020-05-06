<?php

namespace Perevorotcom\Laraveloctober\Facades;

use Illuminate\Support\Facades\Facade;

class Translate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translate';
    }
}
