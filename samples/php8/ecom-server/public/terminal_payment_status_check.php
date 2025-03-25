<?php

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

if (isset($_GET['payment_id'])) {
    $paymentId = $_GET['payment_id'];

    $client = new KodyPayTerminalServiceClient($config['hostname'], [
        'credentials' => ChannelCredentials::createSsl()
    ]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    $request = new PaymentDetailsRequest();
    $request->setStoreId($config['store_id']);
    $request->setOrderId($paymentId);

    list($response, $status) = $client->PaymentDetails($request, $metadata)->wait();

    if ($status->code !== 0) {
        throw new Exception("Error from gRPC: {$status->code} - {$status->details}");
    }

    if (!$response) {
        throw new Exception("No response received from the API");
    }

    $data = [
        'status' => $response->getStatus(),
        'paymentId' => $response->getPaymentId(),
        'paymentReference' => $response->getPaymentReference(),
        'failureReason' => $response->getFailureReason(),
        'dateCreated' => $response->getDateCreated() ?
            date('c', $response->getDateCreated()->getSeconds()) : null
    ];

    $paymentData = $response->getPaymentData();
    if ($paymentData) {
        $data['paymentData'] = [
            'totalAmount' => $paymentData->getTotalAmount(),
            'saleAmount' => $paymentData->getSaleAmount(),
            'tipsAmount' => $paymentData->getTipsAmount(),
            'pspReference' => $paymentData->getPspReference(),
            'receiptJson' => $paymentData->getReceiptJson()
        ];

        if ($paymentData->getDatePaid()) {
            $data['paymentData']['datePaid'] = date('c', $paymentData->getDatePaid()->getSeconds());
        }
    }

    echo json_encode($data);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required payment_id parameter']);
}
