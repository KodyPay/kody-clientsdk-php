<?php

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

function paymentStatus(): void
{
    if (isset($_GET['paymentReference'])) {
        $paymentReference = $_GET['paymentReference'];
        error_log("Making request paymentReference: " . $paymentReference);

        $config = require __DIR__ . '/config.php';

        $client = new KodyEcomPaymentsServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
        $metadata = ['X-API-Key' => [$config['api_key']]];

        $request = new PaymentDetailsRequest();
        $request->setStoreId($config['store_id']);
        $request->setPaymentReference($paymentReference);

        list($response, $status) = $client->PaymentDetails($request, $metadata)->wait();
        error_log("Status Code: " . $status->code);
        error_log("Status Details: " . $status->details);

        if ($status->code === 0) { // Check for success
            if ($response->hasResponse()) {
                $responseData = $response->getResponse();

                // Access fields within the `Response` message using getters
                $paymentId = $responseData->getPaymentId() ?? null;
                $paymentReference = $responseData->getPaymentReference() ?? null;
                $orderId = $responseData->getOrderId() ?? null;
                $status = $responseData->getStatus() ?? null; // status is an enum, can be converted if needed

                // Handle Timestamp fields for dateCreated and datePaid
                $dateCreated = $responseData->getDateCreated()
                    ? $responseData->getDateCreated()->toDateTime()->format('Y-m-d H:i:s')
                    : null;
                $datePaid = $responseData->getDatePaid()
                    ? $responseData->getDatePaid()->toDateTime()->format('Y-m-d H:i:s')
                    : null;

                echo json_encode([
                    'paymentId' => $paymentId,
                    'paymentReference' => $paymentReference,
                    'orderId' => $orderId,
                    'status' => $status,
                    'dateCreated' => $dateCreated,
                    'datePaid' => $datePaid,
                ]);
            } elseif ($response->hasError()) {
                $errorData = $response->getError();

                // Access fields within the `Error` message using getters
                $errorType = $errorData->getType() ?? null;
                $errorMessage = $errorData->getMessage() ?? null;

                echo json_encode([
                    'errorType' => $errorType,
                    'errorMessage' => $errorMessage,
                ]);
            } else {
                error_log("No valid result found in response.");
            }
        } else {
            error_log("No data");
        }
    } else {
        error_log("No payment reference");
    }
}

// Check if the 'status' query parameter is set
if (isset($_GET['status'])) {
    // Get the value of the 'status' query parameter
    $result = $_GET['status'];

    // Define the possible payment outcomes
    $paymentOutcomes = ['success', 'failure', 'expired', 'error'];

    // Check if the result is a valid payment outcome
    if (in_array($result, $paymentOutcomes)) {
        switch ($result) {
            case 'success':
                $message = "Payment was successful!";
                $class = "success";
                break;
            case 'failure':
                $message = "Payment failed. Please try again.";
                $class = "failure";
                break;
            case 'expired':
                $message = "Payment session has expired. Please start again.";
                $class = "expired";
                break;
            case 'error':
                $message = "An error occurred during payment. Please contact support.";
                $class = "error";
                break;
            default:
                $message = "Unknown payment result.";
                $class = "unknown";
                break;
        }
        error_log("Checking status");
        paymentStatus();
    } else {
        $message = "Invalid payment result.";
        $class = "invalid";
    }
} else {
    $message = "No payment result provided.";
    $class = "no-result";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <style>
        .message {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px;
            text-align: center;
        }
        .success { background-color: #d4edda; color: #155724; }
        .failure { background-color: #f8d7da; color: #721c24; }
        .expired { background-color: #fff3cd; color: #856404; }
        .error { background-color: #f8d7da; color: #721c24; }
        .unknown { background-color: #e2e3e5; color: #383d41; }
        .invalid { background-color: #f8d7da; color: #721c24; }
        .no-result { background-color: #e2e3e5; color: #383d41; }
        .links {
            text-align: center;
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        .links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="message <?php echo $class; ?>">
    <?php echo $message; ?>
</div>
<div class="links">
    <a href="checkout.php">New online payment</a> | <a href="index.php">Main menu</a>
</div>
</body>
</html>
