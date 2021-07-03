<?php

namespace Mybit\LaravelDistancematrixAi\Responses;

class Geometry
{
    private $lat;
    private $long;

    public function __construct(float $lat, float $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    public function __toString(): string
    {
        return (string) $this->long . ',' . (string) $this->lat;
    }

    public function getLatitude(): float
    {
        return $this->lat;
    }

    public function getLongitude(): float
    {
        return $this->long;
    }
}
