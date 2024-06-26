<?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kody\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kody\Grpc\Ecom\V1\PaymentInitiationRequest;
use Grpc\ChannelCredentials;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeId = $_POST['store_id'];
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $orderId = $_POST['order_id'];
    $returnUrl = $_POST['return_url'];
    $paymentReference = uniqid('pay_', true);

    $kody_api_hostname = 'grpc.kodypay.com';
    $store_id = '5fa2dd05-1805-494d-b843-fa1a7c34cf8a'; // Use your Kody store ID
    $api_key = ''; // Put your API key

    $client = new KodyEcomPaymentsServiceClient($kody_api_hostname, ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$api_key]];

    $request = new PaymentInitiationRequest();
    $request->setStoreId($storeId);
    $request->setPaymentReference($paymentReference);
    $request->setAmount($amount);
    $request->setCurrency($currency);
    $request->setOrderId($orderId);
    $request->setReturnUrl($returnUrl);

    list($response, $status) = $client->InitiatePayment($request)->wait();

    if ($status->code === \Grpc\STATUS_OK) {
        if ($response->hasResponse()) {
            $paymentUrl = $response->getResponse()->getPaymentUrl();
            header('Location: ' . $paymentUrl);
            exit;
        } else {
            $error = $response->getError()->getMessage();
            header('Location: index.php?error=' . urlencode($error));
            exit;
        }
    } else {
        $error = 'gRPC error: ' . $status->details;
        header('Location: index.php?error=' . urlencode($error));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
