<?php

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;

$config = require __DIR__ . '/config.php';
$functions = require_once __DIR__ . '/functions.php';

/**
* Retrieve payment details with retry mechanism.
*
* Makes multiple attempts to fetch payment details before giving up,
* helping to overcome temporary network or service issues.
*
* @param string $paymentReference The payment reference to query
* @param int $maxRetries Maximum number of retry attempts (default: 3)
* @return array An associative array with payment details on success, or error information
*/
// Modify getPaymentDetails to handle retries
function getPaymentDetails(string $paymentReference, int $maxRetries = 3): array
{
    global $config;
    global $functions;
    
    $attempt = 0;
    $result = [];
    
    while ($attempt < $maxRetries) {
        $attempt++;
        error_log("Attempt {$attempt} for paymentReference: " . $paymentReference);
        
        try {
            // Create the gRPC client
            $client = new KodyEcomPaymentsServiceClient(
                $config['hostname'],
                ['credentials' => ChannelCredentials::createSsl()]
            );
            $metadata = ['X-API-Key' => [$config['api_key']]];
            
            // Build the PaymentDetailsRequest message
            $request = new PaymentDetailsRequest();
            $request->setStoreId($config['store_id']);
            $request->setPaymentReference($paymentReference);
            
            // Make the gRPC call
            list($response, $grpcStatus) = $client->PaymentDetails($request, $metadata)->wait();
            error_log("gRPC Status Code: " . $grpcStatus->code);
            error_log("gRPC Status Details: " . $grpcStatus->details);
            
            // If the gRPC call succeeded, process the response
            if ($grpcStatus->code === 0) {
                // Process the response message
                if ($response->hasResponse()) {
                    $responseData = $response->getResponse();
                    $rawStatus = $responseData->getStatus() ?? null;
                    $mappedStatus = strtolower($functions->getStatusText($rawStatus));
                    return [
                        'success'          => true,
                        'paymentId'        => $responseData->getPaymentId() ?? null,
                        'paymentReference' => $responseData->getPaymentReference() ?? null,
                        'orderId'          => $responseData->getOrderId() ?? null,
                        'status'           => $mappedStatus,
                        'rawStatus'        => $rawStatus,
                        'dateCreated'      => $responseData->getDateCreated()
                            ? $responseData->getDateCreated()->toDateTime()->format('Y-m-d H:i:s')
                            : null,
                        'datePaid'         => $responseData->getDatePaid()
                            ? $responseData->getDatePaid()->toDateTime()->format('Y-m-d H:i:s')
                            : null,
                    ];
                } elseif ($response->hasError()) {
                    $errorData = $response->getError();
                    return [
                        'errorType' => $errorData->getType() ?? null,
                        'errorMessage' => $errorData->getMessage() ?? null
                    ];
                } else {
                    error_log("No valid result found in response.");
                    // Will retry if we haven't exceeded max attempts
                }
            }
            
            if ($attempt >= $maxRetries) {
                // Final attempt failed, return error
                $result['error'] = 'gRPC call failed after ' . $maxRetries . ' attempts. Last status: ' . $grpcStatus->code;
                $result['details'] = $grpcStatus->details;
            } else {
                usleep(1000000); // 1s delay between retries
            }
            
        } catch (Exception $e) {
            error_log("Exception on attempt {$attempt}: " . $e->getMessage());
            if ($attempt >= $maxRetries) {
                $result['error'] = 'Exception after ' . $maxRetries . ' attempts: ' . $e->getMessage();
                return $result;
            }

            usleep(1000000);
        }
    }
    
    return $result;
}

// --- MAIN LOGIC ---

$message = "";
$class = "";

try {
    // Optionally, get an expected status from GET (e.g. "success", "failed", etc.)
    $expectedStatus = isset($_GET['status']) ? strtolower($_GET['status']) : "";

    if (!isset($_GET['paymentReference'])) {
        throw new Exception("Missing payment reference.");
    }
    $paymentReference = $_GET['paymentReference'];

    // Retrieve payment details (PaymentDetails is the source of truth).
    $resultData = getPaymentDetails($paymentReference);

    if (isset($resultData['error'])) {
        $message = "Something went wrong: " . $resultData['error'];
        $class = "error";
    } else {
        // Actual status from PaymentDetails.
        $actualStatus = $resultData['status'] ?? 'unknown';

        // Optional validation: If an expected status is provided and does not match actual status, throw an exception.
        if ($expectedStatus !== "" && $expectedStatus !== $actualStatus) {
            throw new Exception(
                "Expected status ($expectedStatus) does not match actual payment status ($actualStatus)."
            );
        }

        // Determine message based on actual status.
        if ($actualStatus === 'success') {
            $message = "Payment was successful!";
            $class = "success";
        } else {
            $message = "Payment status: " . ucfirst($actualStatus);
            $class = "error";
        }
    }
} catch (Exception $e) {
    $message = "Exception: " . $e->getMessage();
    $class = "error";
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
    <?php echo htmlspecialchars($message); ?>
</div>
<div class="links">
    <a href="checkout.php">New online payment</a> | <a href="index.php">Main menu</a>
</div>
</body>
</html>
