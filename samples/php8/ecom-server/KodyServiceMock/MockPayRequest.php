<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class PayRequest
{
    private $store_id;
    private $amount;
    private $terminal_id;

    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setTerminalId($terminal_id)
    {
        $this->terminal_id = $terminal_id;
    }
}
