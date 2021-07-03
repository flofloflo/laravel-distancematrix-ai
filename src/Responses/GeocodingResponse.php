<?php

namespace Mybit\LaravelDistancematrixAi\Responses;

class GeocodingResponse
{
    public const RESPONSE_STATUS_OK = 'OK';
    public const RESPONSE_STATUS_ZERO_RESULTS = 'ZERO_RESULTS';
    public const RESPONSE_STATUS_OVER_DAILY_LIMIT = 'OVER_DAILY_LIMIT';
    public const RESPONSE_STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
    public const RESPONSE_STATUS_REQUEST_DENIED = 'REQUEST_DENIED';
    public const RESPONSE_STATUS_INVALID_REQUEST = 'INVALID_REQUEST';
    public const RESPONSE_STATUS_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    public const RESPONSE_STATUS = [
        self::RESPONSE_STATUS_OK,
        self::RESPONSE_STATUS_ZERO_RESULTS,
        self::RESPONSE_STATUS_OVER_DAILY_LIMIT,
        self::RESPONSE_STATUS_OVER_QUERY_LIMIT,
        self::RESPONSE_STATUS_REQUEST_DENIED,
        self::RESPONSE_STATUS_INVALID_REQUEST,
        self::RESPONSE_STATUS_UNKNOWN_ERROR,
    ];

    private $formattedAddress;

    private $geometry;

    private $responseObject;

    private $locationType;

    private $status;

    public function __construct(\stdClass $responseObject)
    {
        $this->responseObject = $responseObject;

        $this->buildResponseObject();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResponseObject(): \stdClass
    {
        return $this->responseObject;
    }

    public function getFormattedAddress(): string
    {
        return $this->formattedAddress;
    }

    public function getLocationType(): string
    {
        return $this->locationType;
    }

    public function getGeometry(): Geometry
    {
        return $this->geometry;
    }

    private function buildResponseObject(): void
    {
        $this->status = $this->responseObject->status;

        if (isset($this->responseObject->formattedAddress)) {
            $this->formattedAddress = $this->responseObject->formattedAddress;
        }

        if (isset($this->responseObject->geometry->locationType)) {
            $this->locationType = $this->responseObject->geometry->locationType;
        }

        if (isset($this->responseObject->geometry->location)) {
            $this->geometry = new Geometry(
                $this->responseObject->geometry->location->lat, 
                $this->responseObject->geometry->location->long
            );
        }
    }
}
