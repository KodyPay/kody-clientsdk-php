<?php

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\CancelRequest;
use Grpc\ChannelCredentials;

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['terminal_id'], $_POST['store_id'], $_POST['amount'], $_POST['order_id'])) {

    $terminalId = $_POST['terminal_id'];
    $storeId = $_POST['store_id'];
    $amount = $_POST['amount'];
    $orderId = $_POST['order_id'];

    $client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    $request = new CancelRequest();
    $request->setStoreId($storeId);
    $request->setTerminalId($terminalId);
    $request->setAmount(number_format($amount, 2, '.', ''));
    $request->setOrderId($orderId);

    error_log("Canceling payment:  terminal id [$terminalId] / order id [$orderId] / amount [$amount] ");

    list($response, $status) = $client->Cancel($request, $metadata)->wait();

    error_log("Canceling status: ".$status->code);

    if ($status->code === 0) {
        $data = [
            'status' => 'cancelled',
            'orderId' => $orderId,
            'message' => 'Payment has been successfully cancelled.'
        ];
    } else {
        $data = [
            'status' => 'error',
            'message' => 'An error occurred while cancelling the payment.'
        ];
    }

    echo json_encode($data);

    // Redirect back to the payment form page
    header('Location: terminals.php?status=' . urlencode($data['status']) . '&message=' . urlencode($data['message']));
    exit;
} else {
    echo json_encode(['error' => 'Invalid request']);
}

