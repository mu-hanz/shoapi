<?php

namespace Muhanz\Shoapi\Facades;

use Illuminate\Support\Facades\Facade;

class Shoapi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shoapi';
    }
}
