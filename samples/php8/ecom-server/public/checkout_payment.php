<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentInitiationRequest;
use Com\Kodypay\Grpc\Ecom\V1\PaymentStatus;
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
    $request->setReturnUrl($config['redirect_url'] . '?paymentReference=' . $paymentReference);

    if (isset($_POST['enable_expiration'])) {
        $expiry = new PaymentInitiationRequest\ExpirySettings();

        $expiry->setShowTimer($_POST['show_timer']);
        $expiry->setExpiringSeconds($_POST['expiring_seconds']);

        $request->setExpiry($expiry);
    }

    $timeoutDateTime = (new DateTime())->add(new DateInterval('PT' . (3 * 60) . 'S'));

    $responses = $client->InitiatePaymentStream($request, $metadata, ['timeout' => $timeoutDateTime]) -> responses();

    foreach ($responses as $reply) {
        if ($reply->getResponse()) {
            $status=$reply->getResponse()->getStatus().PHP_EOL;
            if($status != PaymentStatus::PENDING){
                break;
            }
            $paymentId = $reply->getResponse()->getPaymentId().PHP_EOL;;
            $paymentUrl = $reply->getResponse()->getPaymentData()->getPaymentWallet()->getPaymentLinkId().PHP_EOL;;
            $status=$reply->getResponse()->getStatus().PHP_EOL;
            echo "<h2>Collecting payment for Payment ID: $paymentId</h2>";
            echo "<h2>Payment status:$status</h2>";
            echo "<h2>Payment Url: $paymentUrl</h2>";
        } else{
            error_log("Error: Unable to initiate payment.");
            echo "<h2>Error: Unable to initiate payment.</h2>";
            break;
        }
    }

//    if ($paymentId && $paymentStatus == PaymentStatus::PENDING) {
//        echo "<h2>Collecting payment for Payment ID: $paymentId</h2>";
//        echo "<h2>Payment Url: $paymentUrl</h2>";
//        echo "<div id='loading'>Waiting for payment...</div>";
//        echo "<input type='hidden' name='store_id' value='" . htmlspecialchars($config['store_id']) . "'>";
//        echo "<input type='hidden' name='amount' value='" . htmlspecialchars($_POST['amount']) . "'>";
//        echo "<input type='hidden' name='payment_url' value='" . htmlspecialchars($paymentUrl) . "'>";
//        echo "</div>";
//    } elseif ($paymentStatus != PaymentStatus::PENDING){
//            error_log("Payment something");
//            header('Location: checkout.php');
//            exit;
//    }
//    else {
//        error_log("Error: Unable to initiate payment.");
//        echo "<h2>Error: Unable to initiate payment.</h2>";
//    }
} else {
    header('Location: checkout.php');
    exit;
}
