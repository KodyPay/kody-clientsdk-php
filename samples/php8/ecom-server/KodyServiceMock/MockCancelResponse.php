<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class CancelResponse
{
    private $status;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
