<?php

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

$functions = require_once dirname(__DIR__) . '/functions.php';
$config = require dirname(__DIR__) . '/config.php';

function getPaymentDetailsByReference(string $paymentReference): array
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

    return processResponse($response, $grpcStatus);
}

function getPaymentDetailsById(string $paymentId): array
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
    $request->setPaymentId($paymentId);

    // Make the call
    list($response, $grpcStatus) = $client->PaymentDetails($request, $metadata)->wait();

    return processResponse($response, $grpcStatus);
}

function processResponse($response, $grpcStatus): array
{
    global $functions;

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

                'status' => $mappedStatus,
                'statusText' => $statusText,
                'rawStatus' => $rawStatus,

                'paymentData' => [
                    'pspReference' => $responseData->getPaymentData()->getPspReference() ?? null,
                    'paymentMethodVariant' => $responseData->getPaymentData()->getPaymentMethodVariant() ?? null,
                    'authStatus' => $responseData->getPaymentData()->getAuthStatus() ?? null,
                    'authStatusDate' => $responseData->getPaymentData()->getAuthStatusDate()
                        ? date('Y-m-d H:i:s', $responseData->getPaymentData()->getAuthStatusDate()->getSeconds())
                        : null,
                    'paymentCard' => $responseData->getPaymentData()->getPaymentCard()
                        ? [
                            'cardLast4Digits' => $responseData->getPaymentData()->getPaymentCard()->getCardLast4Digits() ?? null,
                            'authCode' => $responseData->getPaymentData()->getPaymentCard()->getAuthCode() ?? null,
                        ]
                        : null,
                    'paymentWallet' => $responseData->getPaymentData()->getPaymentWallet()
                        ? [
                            'paymentLinkId' => $responseData->getPaymentData()->getPaymentWallet()->getPaymentLinkId() ?? null,
                            'cardLast4Digits' => $responseData->getPaymentData()->getPaymentWallet()->getCardLast4Digits() ?? null,
                        ]
                        : null,
                ],

                'saleData' => [
                    'amount' => $responseData->getSaleData()->getAmountMinorUnits() / 100 ?? null,
                    'currency' => $responseData->getSaleData()->getCurrency() ?? null,
                    'paymentReference' => $responseData->getSaleData()->getPaymentReference() ?? null,
                    'orderId' => $responseData->getSaleData()->getOrderId() ?? null,
                    'orderMetadata' => $responseData->getSaleData()->getOrderMetadata() ?? null,
                ],

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

if (isset($_GET['paymentReference'])) {
    $paymentReference = $_GET['paymentReference'];
    $result = getPaymentDetailsByReference($paymentReference);
    echo json_encode($result);
    exit;
} else if (isset($_GET['payment_id'])) {
    $paymentId = $_GET['payment_id'];
    $result = getPaymentDetailsById($paymentId);
    echo json_encode($result);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Missing payment reference or payment ID']);
    exit;
}
