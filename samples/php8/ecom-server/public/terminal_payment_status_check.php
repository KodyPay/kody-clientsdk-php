<?php

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    $client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    $request = new PaymentDetailsRequest();
    $request->setStoreId($config['store_id']);
    $request->setOrderId($orderId);

    list($response, $status) = $client->PaymentDetails($request, $metadata)->wait();

    $status = $response->getStatus();
    $data = [
        'status' => $status,
        'orderId' => $response->getIdempotencyUuid(),
        'totalAmount' => $response->getTotalAmount(),
        'saleAmount' => $response->getSaleAmount(),
        'tipsAmount' => $response->getTipsAmount(),
        'dateCreated' => $response->getDateCreated()->serializeToJsonString(),
        'datePaid' => $response->getDatePaid() ? $response->getDatePaid()->serializeToJsonString() : null,
        'failureReason' => $response->getFailureReason(),
        'pspReference' => $response->getPspReference(),
        'receiptJson' => $response->getReceiptJson()
    ];

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
