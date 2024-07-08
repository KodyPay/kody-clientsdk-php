<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class CancelRequest
{
    private $store_id;
    private $amount;
    private $terminal_id;
    private $order_id;

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

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }
}
