<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class TerminalsRequest
{
    private $store_id;

    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
    }
}
