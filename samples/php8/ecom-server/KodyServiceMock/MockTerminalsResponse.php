<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class TerminalsResponse
{
    private $terminals = [];

    public function setTerminals(array $terminals)
    {
        $this->terminals = $terminals;
    }

    public function getTerminals()
    {
        return $this->terminals;
    }
}
