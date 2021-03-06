<?php

namespace Mybit\LaravelDistancematrixAi\Responses;

class DistanceMatrixResponse
{
    public const RESPONSE_STATUS_OK = 'OK';
    public const RESPONSE_STATUS_INVALID_REQUEST = 'INVALID_REQUEST';
    public const RESPONSE_STATUS_MAX_ELEMENTS_EXCEEDED = 'MAX_ELEMENTS_EXCEEDED';
    public const RESPONSE_STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
    public const RESPONSE_STATUS_REQUEST_DENIED = 'REQUEST_DENIED';
    public const RESPONSE_STATUS_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    public const RESPONSE_STATUS = [
        self::RESPONSE_STATUS_OK,
        self::RESPONSE_STATUS_INVALID_REQUEST,
        self::RESPONSE_STATUS_MAX_ELEMENTS_EXCEEDED,
        self::RESPONSE_STATUS_OVER_QUERY_LIMIT,
        self::RESPONSE_STATUS_REQUEST_DENIED,
        self::RESPONSE_STATUS_UNKNOWN_ERROR,
    ];

    private $destinationAddresses;

    private $originAddresses;

    private $responseObject;

    private $rows;

    private $status;

    public function __construct(\stdClass $responseObject)
    {
        $this->responseObject = $responseObject;
        $this->originAddresses = [];
        $this->destinationAddresses = [];
        $this->rows = [];

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

    public function getOriginAddresses(): array
    {
        return $this->originAddresses;
    }

    public function getDestinationAddresses(): array
    {
        return $this->destinationAddresses;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getFirstRow(): ?Row
    {
        return empty($this->rows) ? null : $this->rows[0];
    }

    public function getFirstElement(): ?Element
    {
        $row = $this->getFirstRow();
        return is_null($row) ? null : $row->getFirstElement();
    }

    private function buildResponseObject(): void
    {
        $this->status = $this->responseObject->status;

        if (isset($this->responseObject->origin_addresses)) {
            foreach ($this->responseObject->origin_addresses as $originAddress) {
                $this->addOriginAddress(new Address($originAddress));
            }
        }

        if (isset($this->responseObject->origin_addresses)) {
            foreach ($this->responseObject->destination_addresses as $destinationAddress) {
                $this->addDestinationAddress(new Address($destinationAddress));
            }
        }

        if (isset($this->responseObject->rows)) {
            foreach ($this->responseObject->rows as $row) {
                $elements = [];
                foreach ($row->elements as $element) {
                    if(!empty($element) && !empty($element->duration) && !empty($element->distance)) {
                        $duration = new Duration($element->duration->text, $element->duration->value);
                        $distance = new Distance($element->distance->text, $element->distance->value);
                        $elements[] = new Element($element->status, $duration, $distance);
                    }
                }
                $this->addRow(new Row($elements));
            }
        }
    }

    private function addOriginAddress(Address $originAddress): void
    {
        $this->originAddresses[] = $originAddress;
    }

    private function addDestinationAddress(Address $destinationAddress): void
    {
        $this->destinationAddresses[] = $destinationAddress;
    }

    private function addRow(Row $row): void
    {
        $this->rows[] = $row;
    }
}
