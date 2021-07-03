# Laravel wrapper for the DISTANCEMATRIX.AI API

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
php artisan vendor:publish --provider="Mybit\LaravelDistancematrixAi\DistanceMatrixServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
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
```

## Usage (DistanceMatrix API)

```php
use Mybit\LaravelDistancematrixAi\Facades\DistanceMatrix;

$distanceResponse = DistanceMatrix::setLanguage('de')
    ->setOrigin('53.54942880970846, 9.95784213616111')
    ->setDestination('53.549626412962326, 9.968088174277717')
    ->sendRequest();
```

**Heads up:** When using `addOrigin()` or `addDestination()` in combination with the facade, you can end up querying the locations of the previous call(s), as the facade will return the same DistanceMatrix instance for the complete application request lifecycle. To prevent this, use `setOrigin()` / `setDestination()` at the beginning of your query as they will reset the internal origins/destinations array.

## Usage (Geocoding API)

See [https://distancematrix.ai/geo](https://distancematrix.ai/geo) for more information on the geocoding API.

```php
use Mybit\LaravelDistancematrixAi\Facades\Geocoding;

$geocodingResponse = Geocoding::setLanguage('de')
    ->setAddress('1600 Amphitheatre Parkway, Mountain View, CA')
    ->setBounds(-124.48200307,32.52952353,-114.13078164,42.00949894)
    ->sendRequest();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Final Bytes](https://github.com/finalbytes/google-distance-matrix-api) - I used his google-distance-matrix-api package as starting point for this implementation
- [Florian Heller](https://github.com/flofloflo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
