# Laravel wrapper for the DISTANCEMATRIX.AI API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mybit/laravel-distancematrix-ai.svg?style=flat-square)](https://packagist.org/packages/mybit/laravel-distancematrix-ai)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/mybit/laravel-distancematrix-ai/run-tests?label=tests)](https://github.com/mybit/laravel-distancematrix-ai/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/mybit/laravel-distancematrix-ai/Check%20&%20fix%20styling?label=code%20style)](https://github.com/mybit/laravel-distancematrix-ai/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/mybit/laravel-distancematrix-ai.svg?style=flat-square)](https://packagist.org/packages/mybit/laravel-distancematrix-ai)

---

Fetches estimated travel time and distance for multiple destinations from the DISTANCEMATRIX.AI API.

## Installation

You can install the package via composer [VCS](https://getcomposer.org/doc/05-repositories.md#vcs) (package currently not available via packagist):

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/flofloflo/laravel-distancematrix-ai"
        }
    ],
    "require": {
        "mybit/laravel-distancematrix-ai": "dev-master"
    }
}
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Mybit\DistanceMatrix\DistanceMatrixServiceProvider" --tag="laravel-distancematrix-ai-config"
```

This is the contents of the published config file:

```php
return [
     /**
     * The API key which should be used for calls to the DISTANCEMATRIX.AI API
     */
    'api_key' => env('DISTANCEMATRIX_API_KEY', null),

    /**
     * Default values
     */
    'defaults' => [
        // Unit system used for distances
        'units' => DistanceMatrix::UNITS_METRIC,
        // Driving mode used for distance calculation
        'mode' => DistanceMatrix::MODE_DRIVING,
    ]
];
```

## Usage

```php
$distanceMatrix = new Mybit\DistanceMatrix();
$distance = $distanceMatrix
    ->setLanguage('de-DE')
    ->addOrigin('53.54942880970846, 9.95784213616111')
    ->addDestination('53.549626412962326, 9.968088174277717')
    ->sendRequest();
```

## Testing

Currently no tests implemented :-(

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Final Bytes](https://github.com/finalbytes/distancematrix-ai-api) - I used his distancematrix-ai-api package as starting point for this implementation
-   [Spatie](https://spatie.be/open-source) - Using their package skeleton
-   [Florian Heller](https://github.com/flofloflo)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
