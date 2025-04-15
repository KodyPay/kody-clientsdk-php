<?php
$config = require dirname(__DIR__) . '/config.php';
$functions = require_once dirname(__DIR__) . '/functions.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\GetPaymentsRequest;
use Grpc\ChannelCredentials;

// Get current page from query parameter
$currentPage = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
$pageSize = isset($_GET['pageSize']) ? max(1, intval($_GET['pageSize'])) : 16;

// Create gRPC client
$client = new KodyEcomPaymentsServiceClient(
    $config['hostname'],
    ['credentials' => ChannelCredentials::createSsl()]
);

// Set up metadata with API key
$metadata = ['X-API-Key' => [$config['api_key']]];

// Create request
$request = new GetPaymentsRequest();
$request->setStoreId($config['store_id']);

// Set up pagination
$pageCursor = new GetPaymentsRequest\PageCursor();
$pageCursor->setPage($currentPage);
$pageCursor->setPageSize($pageSize);
$request->setPageCursor($pageCursor);

header('Content-Type: application/json');

// Make the gRPC call
try {
    list($response, $status) = $client->GetPayments($request, $metadata)->wait();

    if ($status->code !== \Grpc\STATUS_OK) {
        error_log("gRPC error: Code=" . $status->code . " Details=" . $status->details);
        die(json_encode([
            'success' => false,
            'error' => $status->details
        ]));
    }

    // Check if we have a response
    if ($response->hasResponse()) {
        $responseData = $response->getResponse();
        $payments = [];
        $total = $responseData->getTotal();
        $totalPages = ceil($total / $pageSize);

        // Get all payments
        foreach ($responseData->getPayments() as $payment) {
            $paymentData = [
                'payment_id' => $payment->getPaymentId(),
                'payment_reference' => $payment->getPaymentReference(),
                'order_id' => $payment->getOrderId(),
                'status' => $payment->getStatus(),
                'status_text' => $functions->getStatusText($payment->getStatus()),
                'psp_reference' => $payment->getPspReference(),
                'payment_method' => $payment->getPaymentMethod()
            ];

            // Handle optional fields
            if ($payment->hasDateCreated()) {
                $dateCreated = $payment->getDateCreated();
                $paymentData['date_created'] = date('Y-m-d H:i:s', $dateCreated->getSeconds());
            }

            if ($payment->hasDatePaid()) {
                $datePaid = $payment->getDatePaid();
                $paymentData['date_paid'] = date('Y-m-d H:i:s', $datePaid->getSeconds());
            }

            $payments[] = $paymentData;
        }

        echo json_encode([
            'success' => true,
            'payments' => $payments,
            'pagination' => [
                'currentPage' => $currentPage,
                'pageSize' => $pageSize,
                'totalItems' => $total,
                'totalPages' => $totalPages
            ]
        ]);

    } elseif ($response->hasError()) {
        $error = $response->getError();
        error_log("Application error: Type=" . $error->getType() . " Message=" . $error->getMessage());
        die(json_encode([
            'success' => false,
            'errorType' => $error->getType(),
            'errorMessage' => $error->getMessage()
        ]));
    }

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    die(json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred',
        'details' => $e->getMessage()
    ]));
}
