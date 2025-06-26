<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentInitiationRequest;
use Grpc\ChannelCredentials;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentReference = uniqid('pay_', true);

    $client = new KodyEcomPaymentsServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    $request = new PaymentInitiationRequest();

    $request->setStoreId($config['store_id']);
    $request->setPaymentReference($paymentReference);
    $request->setAmountMinorUnits($_POST['amount']);
    $request->setCurrency($_POST['currency']);
    $request->setOrderId($_POST['order_id']);
    // $request->setIsPayByBankAccepted($_POST['enable_pay_by_bank']);
    $request->setReturnUrl($config['redirect_url'].'?paymentReference='.$paymentReference);

    if (isset($_POST['enable_expiration'])) {
        $expiry = new PaymentInitiationRequest\ExpirySettings();

        $expiry->setShowTimer($_POST['show_timer']);
        $expiry->setExpiringSeconds($_POST['expiring_seconds']);

        $request->setExpiry($expiry);
    }

    list($response, $status) = $client->InitiatePayment($request, $metadata)->wait();

    if ($status->code === \Grpc\STATUS_OK) {
        if ($response->hasResponse()) {
            $paymentUrl = $response->getResponse()->getPaymentUrl();
            header('Location: ' . $paymentUrl);
            exit;
        } else {
            $error = $response->getError()->getMessage();
            header('Location: checkout.php?error=' . urlencode($error));
            exit;
        }
    } else {
        $error = 'gRPC error: ' . $status->details;
        header('Location: checkout.php?error=' . urlencode($error));
        exit;
    }
} else {
    header('Location: checkout.php');
    exit;
}
