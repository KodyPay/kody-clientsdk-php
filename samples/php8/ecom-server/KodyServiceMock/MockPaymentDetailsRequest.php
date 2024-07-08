<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class PaymentDetailsRequest
{
    private $store_id;
    private $order_id;

    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }
}
