<?php

namespace Mybit\LaravelDistancematrixAi\Responses;

class Row
{
    private $elements;

    public function __construct($elements)
    {
        $this->elements = $elements;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function getFirstElement(): ?Element
    {
        return empty($this->elements) ? null : $this->elements[0];
    }
}
