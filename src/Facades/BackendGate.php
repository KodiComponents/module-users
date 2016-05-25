<?php

namespace KodiCMS\Users\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class BackendGate
 * @package KodiCMS\Users\Facades
 */
class BackendGate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.gate';
    }
}
