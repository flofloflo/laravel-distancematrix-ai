<?php

use Mybit\LaravelDistancematrixAi\DistanceMatrix;

return [
    /**
     * The API key which should be used for calls to the DISTANCEMATRIX.AI API
     */
    'api_key' => env('DISTANCEMATRIX_API_KEY', null),

    /**
     * Default values
     */
    'defaults' => [
        // The language which is used for returning the results
        // see https://distancematrix.ai/dev#request_parameters for a list of supported values
        'language' => 'en',
        // Unit system used for distances
        'units' => DistanceMatrix::UNITS_METRIC,
        // Driving mode used for distance calculation
        'mode' => DistanceMatrix::MODE_DRIVING,
        // Route restrictions
        'avoid' => null,
    ]
];
