<?php

namespace Mybit\LaravelDistancematrixAi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mybit\LaravelDistancematrixAi\Geocoding
 */
class Geocoding extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-distancematrix-ai-geocoding';
    }
}
