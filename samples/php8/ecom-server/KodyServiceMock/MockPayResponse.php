<?php

namespace Com\Kodypay\Grpc\Pay\V1;

use Google\Protobuf\Timestamp;

class PayResponse
{
    private $status;
    private $failure_reason;
    private $receipt_json;
    private $order_id;
    private $date_created;
    private $ext_payment_ref;
    private $date_paid;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function setDateCreated(Timestamp $date_created)
    {
        $this->date_created = $date_created;
    }

    public function setDatePaid(Timestamp $date_paid)
    {
        $this->date_paid = $date_paid;
    }

    public function setFailureReason($failure_reason)
    {
        $this->failure_reason = $failure_reason;
    }

    public function setReceiptJson($receipt_json)
    {
        $this->receipt_json = $receipt_json;
    }

    public function setExtPaymentRef($ext_payment_ref)
    {
        $this->ext_payment_ref = $ext_payment_ref;
    }
}
