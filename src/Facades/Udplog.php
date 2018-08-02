<?php

namespace Bulaohe\Udplog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * see \App\Services\JlogService
 * @author Quan
 *
 */
class Udplog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'udplog';
    }
}