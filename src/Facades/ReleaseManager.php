<?php

namespace IBroStudio\ReleaseManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IBroStudio\ReleaseManager\ReleaseManager
 */
class ReleaseManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \IBroStudio\ReleaseManager\ReleaseManager::class;
    }
}
