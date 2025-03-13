<?php

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

$functions = require_once dirname(__DIR__) . '/functions.php';
$config = require dirname(__DIR__) . '/config.php';

function getPaymentDetails(string $paymentReference): array
{
    global $config;
    global $functions;

    // Create the gRPC client
    $client = new KodyEcomPaymentsServiceClient(
        $config['hostname'],
        ['credentials' => ChannelCredentials::createSsl()]
    );
    $metadata = ['X-API-Key' => [$config['api_key']]];

    // Build the request
    $request = new PaymentDetailsRequest();
    $request->setStoreId($config['store_id']);
    $request->setPaymentReference($paymentReference);

    // Make the call
    list($response, $grpcStatus) = $client->PaymentDetails($request, $metadata)->wait();

    if ($grpcStatus->code === 0) {
        if ($response->hasResponse()) {
            $responseData = $response->getResponse();
            $rawStatus = $responseData->getStatus() ?? null;

            // Map the status using the functions class
            $statusText = $functions->getStatusText($rawStatus);
            $mappedStatus = strtolower($statusText);

            return [
                'success' => true,
                'paymentId' => $responseData->getPaymentId() ?? null,
                'paymentReference' => $responseData->getPaymentReference() ?? null,
                'orderId' => $responseData->getOrderId() ?? null,
                'status' => $mappedStatus,
                'statusText' => $statusText,
                'rawStatus' => $rawStatus,
                'dateCreated' => $responseData->getDateCreated()
                    ? $responseData->getDateCreated()->toDateTime()->format('Y-m-d H:i:s')
                    : null,
                'datePaid' => $responseData->getDatePaid()
                    ? $responseData->getDatePaid()->toDateTime()->format('Y-m-d H:i:s')
                    : null,
            ];
        } elseif ($response->hasError()) {
            $errorData = $response->getError();
            return [
                'success' => false,
                'errorType' => $errorData->getType() ?? null,
                'errorMessage' => $errorData->getMessage() ?? null
            ];
        }
    }

    return [
        'success' => false,
        'error' => 'gRPC call failed with status code ' . $grpcStatus->code,
        'details' => $grpcStatus->details
    ];
}

header('Content-Type: application/json');

if (!isset($_GET['paymentReference'])) {
    echo json_encode(['success' => false, 'error' => 'Missing payment reference']);
    exit;
}

$paymentReference = $_GET['paymentReference'];
$result = getPaymentDetails($paymentReference);
echo json_encode($result);
