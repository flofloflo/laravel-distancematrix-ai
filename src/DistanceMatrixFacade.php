<?php

namespace Mybit\DistanceMatrix;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mybit\DistanceMatrix\DistanceMatrix
 */
class DistanceMatrixFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-distancematrix-ai';
    }
}
