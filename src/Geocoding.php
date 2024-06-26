<?php

namespace Mybit\LaravelDistancematrixAi;

use GuzzleHttp\Client;
use Mybit\LaravelDistancematrixAi\Responses\GeocodingResponse;
use Mybit\LaravelDistancematrixAi\Traits\AllowedLanguages;

class Geocoding
{
    use AllowedLanguages;

    private const API_URL = 'https://api.distancematrix.ai/maps/api/geocode/json';
    private const API_URL_FAST = 'https://api-v2.distancematrix.ai/maps/api/geocode/json';
    private const LANGUAGE = 'en';

    // Geocoding
    private $language;
    private $address;
    private $bounds = [];

    private function apiUrl(bool $fast = false): string
    {
        return $fast ? self::API_URL_FAST : self::API_URL;
    }

    public function getApiKey(bool $fast = false): ?string
    {
        if ($fast) {
            return config('distancematrix-ai.api_keys.gc_fast');
        }
        return config('distancematrix-ai.api_keys.gc_accurate');
    }

    public function getLanguage(): string
    {
        if (is_null($this->language)) {
            $this->language = config('distancematrix-ai.defaults.language', self::LANGUAGE);
        }

        return $this->language;
    }

    public function setLanguage($language): Geocoding
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): Geocoding
    {
        $this->address = $address;

        return $this;
    }

    public function getBounds(): array
    {
        return $this->bounds;
    }

    public function setBounds(float $minLong, float $minLat, float $maxLong, float $maxLat): Geocoding
    {
        $this->bounds = [
            $minLong,
            $minLat,
            $maxLong,
            $maxLat,
        ];

        return $this;
    }

    public function setBoundsArray(array $bounds): Geocoding
    {
        $this->bounds = $bounds;

        return $this;
    }

    /**
     * Sends a request to the DistanceMatrix.AI Geocoding API
     *
     * @return GeocodingResponse|null
     */
    public function sendRequest(bool $fast = false): ?GeocodingResponse
    {
        if (is_null($this->getApiKey($fast))) {
            return null;
        }
        $this->validateRequest();
        $data = [
            'key' => $this->getApiKey($fast),
            'language' => $this->getLanguage(),
            'address' => $this->getAddress(),
        ];
        if (!empty($this->getBounds())) {
            $data['bounds'] = implode(',', $this->getBounds());
        }
        $parameters = http_build_query($data);
        $url = $this->apiUrl($fast)  . '?' . $parameters;

        return $this->request('GET', $url);
    }

    private function validateRequest(): void
    {
        if (empty($this->getAddress())) {
            throw new Exceptions\AddressException('The address must be set.');
        }
        if (!empty($this->getBounds() && count($this->getBounds()) != 4)) {
            throw new Exceptions\BoundsException('The bounding box must contain of four points!');
        }
    }

    private function request($type = 'GET', $url): GeocodingResponse
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
        $responseObject = new GeocodingResponse(json_decode($response->getBody()->getContents()));
        $this->validateResponse($responseObject);

        return $responseObject;
    }

    private function validateResponse(GeocodingResponse $response): void
    {
        switch ($response->getStatus()) {
            case GeocodingResponse::RESPONSE_STATUS_OK:
                break;
            case GeocodingResponse::RESPONSE_STATUS_ZERO_RESULTS:
                break;
            case GeocodingResponse::RESPONSE_STATUS_INVALID_REQUEST:
                throw new Exceptions\ResponseException("Invalid request.", 1);
            case GeocodingResponse::RESPONSE_STATUS_OVER_DAILY_LIMIT:
                throw new Exceptions\ResponseException("The service has received too many requests from your application for today.", 3);
            case GeocodingResponse::RESPONSE_STATUS_OVER_QUERY_LIMIT:
                throw new Exceptions\ResponseException("The service has received too many requests from your application in the allowed time range.", 3);
            case GeocodingResponse::RESPONSE_STATUS_REQUEST_DENIED:
                throw new Exceptions\ResponseException("The service denied the use of the Distance Matrix API service by your application.", 4);
            case GeocodingResponse::RESPONSE_STATUS_UNKNOWN_ERROR:
                throw new Exceptions\ResponseException("Unknown error.", 5);
            default:
                throw new Exceptions\ResponseException(sprintf("Unknown status code: %s", $response->getStatus()), 6);
        }
    }
}
