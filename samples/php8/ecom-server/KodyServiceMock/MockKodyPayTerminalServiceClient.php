<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class KodyPayTerminalServiceClient
{
    public function Terminals($request, $metadata = [], $options = [])
    {
        $response = new TerminalsResponse();
        $terminal = new Terminal();
        $terminal->setTerminalId('MOCK_TERMINAL_1')->setOnline(true);
        $response->setTerminals([$terminal]);
        return [$response, null];
    }

    public function Pay($request, $metadata = [], $options = [])
    {
        // Mock response stream with a generator
        return new class {
            public function responses()
            {
                yield (new PayResponse())->setStatus(PaymentStatus::PENDING)->setOrderId('MOCK_ORDER_ID');
                yield (new PayResponse())->setStatus(PaymentStatus::SUCCESS);
            }
        };
    }

    public function Cancel($request, $metadata = [], $options = [])
    {
        $response = new CancelResponse();
        $response->setStatus(PaymentStatus::CANCELLED);
        return [$response, null];
    }

    public function PaymentDetails($request, $metadata = [], $options = [])
    {
        $response = new PayResponse();
        $response->setStatus(PaymentStatus::SUCCESS)
                 ->setOrderId($request->getOrderId())
                 ->setDateCreated((new \Google\Protobuf\Timestamp())->setSeconds(time()))
                 ->setDatePaid((new \Google\Protobuf\Timestamp())->setSeconds(time() + 600))
                 ->setFailureReason('')
                 ->setExtPaymentRef('MOCK_EXT_REF')
                 ->setReceiptJson(json_encode(['items' => [['name' => 'Mock Item', 'price' => 1000]]]));
        return [$response, null];
    }
}
