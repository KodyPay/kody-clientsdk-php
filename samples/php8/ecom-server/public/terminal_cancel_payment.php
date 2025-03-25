<?php

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\CancelRequest;
use Grpc\ChannelCredentials;

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['terminal_id'], $_POST['store_id'], $_POST['amount'])) {

    $terminalId = $_POST['terminal_id'];
    $storeId = $_POST['store_id'];
    $amount = $_POST['amount'];
    $paymentId = isset($_POST['payment_id']) ? $_POST['payment_id'] : null;

    $client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    $request = new CancelRequest();
    $request->setStoreId($storeId);
    $request->setTerminalId($terminalId);
    $request->setAmount(number_format((float)$amount, 2, '.', ''));

    if ($paymentId) {
        $request->setPaymentId($paymentId);
    }

    error_log("Canceling payment: terminal id [$terminalId] / payment id [$paymentId] / amount [$amount]");

    list($response, $status) = $client->Cancel($request, $metadata)->wait();

    error_log("Canceling status: " . $status->code);

    if ($status->code === 0) {
        $data = [
            'status' => 'cancelled',
            'paymentId' => $paymentId,
            'message' => 'Payment has been successfully cancelled.'
        ];
    } else {
        $data = [
            'status' => 'error',
            'code' => $status->code,
            'details' => $status->details,
            'message' => 'An error occurred while cancelling the payment.'
        ];
    }

    // Redirect back to the payment form page
    header('Location: terminals.php?status=' . urlencode($data['status']) . '&message=' . urlencode($data['message']));
    exit;
} else {
    $data = ['error' => 'Invalid request - missing required parameters'];
    error_log("Invalid request to cancel payment: missing parameters");

    header('Location: terminals.php?status=error&message=' . urlencode('Invalid request - missing required parameters'));
    exit;
}
