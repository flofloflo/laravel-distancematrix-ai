<?php

namespace Mybit\LaravelDistancematrixAi;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mybit\LaravelDistancematrixAi\Skeleton\SkeletonClass
 */
class DistanceMatrixFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-distancematrix-ai';
    }
}
