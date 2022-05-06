<?php

namespace Mybit\LaravelDistancematrixAi;

use GuzzleHttp\Client;
use Mybit\LaravelDistancematrixAi\Responses\DistanceMatrixResponse;
use Mybit\LaravelDistancematrixAi\Traits\AllowedLanguages;

class DistanceMatrix
{
    use AllowedLanguages;

    public const AVOID_TOLLS = 'tolls';
    public const AVOID_HIGHWAYS = 'highways';
    public const AVOID_FERRIES = 'ferries';
    public const AVOID_INDOOR = 'indoor';

    public const MODE_BICYCLING = 'bicycling';
    public const MODE_DRIVING = 'driving';
    public const MODE_TRANSIT = 'transit';
    public const MODE_WALKING = 'walking';

    public const UNITS_IMPERIAL = 'imperial';
    public const UNITS_METRIC = 'metric';

    private const API_URL = 'https://api.distancematrix.ai/maps/api/distancematrix/json';
    private const LANGUAGE = 'en';

    private $avoid;
    private $destinations;
    private $language;
    private $mode;
    private $origins;
    private $units;

    public function getApiKey(): ?string
    {
        return config('distancematrix-ai.api_key');
    }

    public function getLanguage(): string
    {
        if (is_null($this->language)) {
            $this->language = config('distancematrix-ai.defaults.language', self::LANGUAGE);
        }

        return $this->language;
    }

    public function setLanguage($language): DistanceMatrix
    {
        if (in_array($language, $this->allowedLanguages)) {
            $this->language = $language;
        } elseif (in_array(strtolower(explode('-', $language, 2)[0]), $this->allowedLanguages)) {
            $this->language = strtolower(explode('-', $language, 2)[0]);
        } else {
            $this->language = null;
        }
        return $this;
    }

    public function getUnits(): string
    {
        if (is_null($this->units)) {
            $this->units = config('distancematrix-ai.defaults.units', self::UNITS_METRIC);
        }

        return $this->units;
    }

    public function setUnits($units): DistanceMatrix
    {
        $this->units = $units;

        return $this;
    }

    public function getOrigins(): array
    {
        return $this->origins;
    }

    public function addOrigin($origin): DistanceMatrix
    {
        $this->origins[] = $origin;

        return $this;
    }

    public function setOrigin($origin): DistanceMatrix
    {
        $this->origins = [$origin];

        return $this;
    }

    public function getDestinations(): array
    {
        return $this->destinations;
    }

    public function addDestination($destination): DistanceMatrix
    {
        $this->destinations[] = $destination;

        return $this;
    }

    public function setDestination($destination): DistanceMatrix
    {
        $this->destinations = [$destination];

        return $this;
    }

    public function getMode(): string
    {
        if (is_null($this->mode)) {
            $this->mode = config('distancematrix-ai.defaults.mode', self::MODE_DRIVING);
        }

        return $this->mode;
    }

    public function setMode(string $mode): DistanceMatrix
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Returns the configured route restrictions
     *
     * @return string|null
     */
    public function getAvoid(): ?string
    {
        if (is_null($this->avoid)) {
            $this->avoid = config('distancematrix-ai.defaults.avoid');
        }

        return $this->avoid;
    }

    /**
     * Sets the route restrictions
     *
     * @param string|null $avoid
     * @return DistanceMatrix
     */
    public function setAvoid(?string $avoid): DistanceMatrix
    {
        $this->avoid = $avoid;

        return $this;
    }

    /**
     * Sends a request to the DistanceMatrix.AI API
     *
     * @return DistanceMatrixResponse|null
     */
    public function sendRequest(): ?DistanceMatrixResponse
    {
        if (is_null($this->getApiKey())) {
            return null;
        }
        $this->validateRequest();
        $data = [
            'key' => $this->getApiKey(),
            'language' => $this->getLanguage(),
            'origins' => count($this->origins) > 1 ? implode('|', $this->origins) : $this->origins[0],
            'destinations' => count($this->destinations) > 1 ? implode('|', $this->destinations) : $this->destinations[0],
            'mode' => $this->getMode(),
            'avoid' => $this->getAvoid(),
            'units' => $this->getUnits(),
        ];
        $parameters = http_build_query($data);
        $url = self::API_URL . '?' . $parameters;

        return $this->request($url);
    }

    private function validateRequest(): void
    {
        if (empty($this->getOrigins())) {
            throw new Exceptions\OriginException('The origin must be set.');
        }
        if (empty($this->getDestinations())) {
            throw new Exceptions\DestinationException('The destination must be set.');
        }
    }

    private function request($url, $type = 'GET'): DistanceMatrixResponse
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Laravel DistanceMatrix.AI wrapper/v1.0',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br',
            ]
        ]);
        $response = $client->request($type, $url);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Response with status code ' . $response->getStatusCode());
        }
        $responseObject = new DistanceMatrixResponse(json_decode($response->getBody()->getContents()));
        $this->validateResponse($responseObject);

        return $responseObject;
    }

    private function validateResponse(DistanceMatrixResponse $response): void
    {
        switch ($response->getStatus()) {
            case DistanceMatrixResponse::RESPONSE_STATUS_OK:
                break;
            case DistanceMatrixResponse::RESPONSE_STATUS_INVALID_REQUEST:
                throw new Exceptions\ResponseException("Invalid request.", 1);
            case DistanceMatrixResponse::RESPONSE_STATUS_MAX_ELEMENTS_EXCEEDED:
                throw new Exceptions\ResponseException("The product of the origin and destination exceeds the limit per request.", 2);
            case DistanceMatrixResponse::RESPONSE_STATUS_OVER_QUERY_LIMIT:
                throw new Exceptions\ResponseException("The service has received too many requests from your application in the allowed time range.", 3);
            case DistanceMatrixResponse::RESPONSE_STATUS_REQUEST_DENIED:
                throw new Exceptions\ResponseException("The service denied the use of the Distance Matrix API service by your application.", 4);
            case DistanceMatrixResponse::RESPONSE_STATUS_UNKNOWN_ERROR:
                throw new Exceptions\ResponseException("Unknown error.", 5);
            default:
                throw new Exceptions\ResponseException(sprintf("Unknown status code: %s", $response->getStatus()), 6);
        }
    }
}
