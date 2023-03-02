<?php

namespace iAmBiB\Hooks\Facades;

use Illuminate\Support\Facades\Facade;

class Hooks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hooks';
    }
}
