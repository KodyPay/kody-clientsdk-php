<?php
// Start output buffering to prevent any accidental output before JSON
ob_start();

// Set content type early
header('Content-Type: application/json');

// Enhanced error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$config = require __DIR__ . '/../config.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PayWithCardTokenRequest;
use Grpc\ChannelCredentials;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    // Validate required fields
    $requiredFields = ['payment_token', 'amount_minor_units', 'currency', 'payment_reference'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate amount is positive integer
    if (!is_numeric($input['amount_minor_units']) || intval($input['amount_minor_units']) <= 0) {
        throw new Exception('Amount must be a positive number');
    }

    // Initialize gRPC client
    $client = new KodyEcomPaymentsServiceClient(
        $config['hostname'],
        ['credentials' => ChannelCredentials::createSsl()]
    );

    $metadata = ['X-API-Key' => [$config['api_key']]];

    // Create the PayWithCardToken request
    $request = new PayWithCardTokenRequest();
    $request->setStoreId($config['store_id']);
    $request->setIdempotencyUuid(uniqid('', true)); // Generate unique idempotency key
    $request->setPaymentToken($input['payment_token']);
    $request->setAmountMinorUnits(intval($input['amount_minor_units']));
    $request->setCurrency($input['currency']);
    $request->setPaymentReference($input['payment_reference']);

    // Set optional fields if provided
    if (isset($input['order_id']) && $input['order_id'] !== '') {
        $request->setOrderId($input['order_id']);
    }

    if (isset($input['payer_statement']) && $input['payer_statement'] !== '') {
        $request->setPayerStatement($input['payer_statement']);
    }

    if (isset($input['order_metadata']) && $input['order_metadata'] !== '') {
        $request->setOrderMetadata($input['order_metadata']);
    }

    // Execute the request with retry logic
    $maxRetries = 3;
    $retryDelay = 1; // seconds

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            list($response, $status) = $client->PayWithCardToken($request, $metadata)->wait();

            if ($status->code === \Grpc\STATUS_OK) {
                if ($response->hasResponse()) {
                    $paymentDetails = $response->getResponse();

                    // Build response data
                    $responseData = [
                        'success' => true,
                        'payment_id' => $paymentDetails->getPaymentId(),
                        'status' => $paymentDetails->getStatus(),
                        'date_created' => $paymentDetails->getDateCreated() ?
                            $paymentDetails->getDateCreated()->toDateTime()->format('Y-m-d H:i:s') : null
                    ];

                    // Add sale data if available
                    if ($paymentDetails->hasSaleData()) {
                        $saleData = $paymentDetails->getSaleData();
                        $responseData['sale_data'] = [
                            'amount_minor_units' => $saleData->getAmountMinorUnits(),
                            'currency' => $saleData->getCurrency(),
                            'order_id' => $saleData->getOrderId(),
                            'payment_reference' => $saleData->getPaymentReference(),
                            'order_metadata' => $saleData->getOrderMetadata()
                        ];
                    }

                    // Add payment data if available
                    if ($paymentDetails->hasPaymentData()) {
                        $paymentData = $paymentDetails->getPaymentData();
                        $responseData['payment_data'] = [
                            'psp_reference' => $paymentData->getPspReference(),
                            'payment_method' => $paymentData->getPaymentMethod(),
                            'payment_method_variant' => $paymentData->getPaymentMethodVariant(),
                            'auth_status' => $paymentData->getAuthStatus(),
                            'auth_status_date' => $paymentData->getAuthStatusDate() ?
                                $paymentData->getAuthStatusDate()->toDateTime()->format('Y-m-d H:i:s') : null
                        ];

                        // Add card details if it's a card payment
                        if ($paymentData->hasPaymentCard()) {
                            $card = $paymentData->getPaymentCard();
                            $responseData['payment_data']['card'] = [
                                'card_last_4_digits' => $card->getCardLast4Digits(),
                                'auth_code' => $card->getAuthCode()
                            ];
                        }

                        // Add wallet details if it's a wallet payment
                        if ($paymentData->hasPaymentWallet()) {
                            $wallet = $paymentData->getPaymentWallet();
                            $responseData['payment_data']['wallet'] = [
                                'card_last_4_digits' => $wallet->getCardLast4Digits(),
                                'payment_link_id' => $wallet->getPaymentLinkId()
                            ];
                        }
                    }

                    ob_end_clean();
                    echo json_encode($responseData);
                    exit;

                } else if ($response->hasError()) {
                    $error = $response->getError();
                    throw new Exception("Payment failed: " . $error->getMessage());
                } else {
                    throw new Exception("Unexpected response format");
                }
            } else {
                throw new Exception("gRPC error: " . $status->details);
            }

        } catch (Exception $e) {
            if ($attempt === $maxRetries) {
                throw $e; // Re-throw on final attempt
            }
            sleep($retryDelay);
            $retryDelay *= 2; // Exponential backoff
        }
    }

} catch (Exception $e) {
    error_log("PayWithCardToken API Error: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("PayWithCardToken PHP Error: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'PHP Error: ' . $e->getMessage()
    ]);
}
?>
