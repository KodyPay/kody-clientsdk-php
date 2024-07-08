<?php

namespace KodyPayTerminalDemo;

require __DIR__ . '/../vendor/autoload.php';

use KodyPayTerminalDemo\KodyTerminalClient;

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    $client = new KodyTerminalClient();
    $paymentDetails = $client->getDetails($orderId);

    echo json_encode([
        'status' => $paymentDetails->getStatus(),
        'order_id' => $paymentDetails->getOrderId(),
        'date_created' => $paymentDetails->getDateCreated(),
        'date_paid' => $paymentDetails->getDatePaid(),
        'failure_reason' => $paymentDetails->getFailureReason(),
        'ext_payment_ref' => $paymentDetails->getExtPaymentRef(),
        'receipt_json' => $paymentDetails->getReceiptJson(),
    ]);
} else {
    echo json_encode(['status' => 'ERROR', 'message' => 'Invalid request.']);
}
