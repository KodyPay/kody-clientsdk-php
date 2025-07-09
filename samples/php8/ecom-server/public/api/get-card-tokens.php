<?php
// Start output buffering to prevent any accidental output before JSON
ob_start();

// Set content type early
header('Content-Type: application/json');

// Enhanced error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\GetCardTokensRequest;
use Grpc\ChannelCredentials;

// Fixed logging function
function debugLog($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] DEBUG: $message";

    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data, JSON_PRETTY_PRINT);
    }

    error_log($logMessage);
}

try {
    // Check PHP version and extensions
    if (!extension_loaded('grpc')) {
        throw new Exception("gRPC extension is not loaded.");
    }

    // Check if required files exist
    $configPath = dirname(__DIR__) . '/config.php';

    if (!file_exists($configPath)) {
        throw new Exception("Config file not found: $configPath");
    }

    // Load config
    $config = require $configPath;

    // Validate config
    $requiredConfigKeys = ['hostname', 'api_key', 'store_id'];
    foreach ($requiredConfigKeys as $key) {
        if (empty($config[$key])) {
            throw new Exception("Missing required config key: $key");
        }
    }

    // Check if SDK classes exist
    if (!class_exists('Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient')) {
        throw new Exception("Kody SDK classes not found. Please ensure the SDK is properly installed.");
    }

    function getCardTokens(array $requestData, array $config): array
    {
        debugLog("Getting card tokens", ['payer_reference' => $requestData['payer_reference']]);

        try {
            // Create the gRPC client with basic SSL
            $client = new KodyEcomPaymentsServiceClient(
                $config['hostname'],
                [
                    'credentials' => ChannelCredentials::createSsl()
                ]
            );

            $metadata = ['X-API-Key' => [$config['api_key']]];

            // Build the request
            $request = new GetCardTokensRequest();

            // Set store_id from config, not from request data
            $request->setStoreId($config['store_id']);

            // Set required fields
            $request->setPayerReference($requestData['payer_reference']);

            // Try multiple connection attempts
            $maxRetries = 3;
            $retryDelay = 2; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    list($response, $grpcStatus) = $client->GetCardTokens($request, $metadata)->wait();

                    debugLog("gRPC call completed", [
                        'attempt' => $attempt,
                        'status_code' => $grpcStatus->code
                    ]);

                    // If successful or non-retryable error, break
                    if ($grpcStatus->code === 0 || !in_array($grpcStatus->code, [14, 4, 8])) {
                        break;
                    }

                    // If this was the last attempt, don't sleep
                    if ($attempt < $maxRetries) {
                        debugLog("Retrying due to status code: " . $grpcStatus->code);
                        sleep($retryDelay);
                    }

                } catch (Exception $e) {
                    debugLog("Exception on attempt $attempt", ['message' => $e->getMessage()]);
                    if ($attempt === $maxRetries) {
                        throw $e;
                    }
                    sleep($retryDelay);
                }
            }

            return processResponse($response, $grpcStatus);

        } catch (Exception $e) {
            debugLog("Exception in getCardTokens", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get card tokens: ' . $e->getMessage()
            ];
        }
    }

    function processResponse($response, $grpcStatus): array
    {
        if ($grpcStatus->code === 0) { // SUCCESS
            if ($response && $response->hasResponse()) {
                $responseData = $response->getResponse();
                $tokens = [];

                // Process each token from the response
                foreach ($responseData->getTokens() as $token) {
                    $tokenData = [
                        'token_id' => $token->getTokenId(),
                        'payment_token' => $token->getPaymentToken(),
                        'payer_reference' => $token->getPayerReference(),
                        'recurring_processing_model' => $token->getRecurringProcessingModel(),
                        'status' => $token->getStatus(),
                        'created_at' => $token->getCreatedAt() ? $token->getCreatedAt()->toDateTime()->format('c') : null,
                        'payment_method' => $token->getPaymentMethod(),
                        'payment_method_variant' => $token->getPaymentMethodVariant(),
                        'funding_source' => $token->getFundingSource(),
                        'card_last_4_digits' => $token->getCardLast4Digits()
                    ];

                    // Add optional fields if present
                    if ($token->getTokenReference()) {
                        $tokenData['token_reference'] = $token->getTokenReference();
                    }

                    $tokens[] = $tokenData;
                }

                debugLog("Card tokens retrieved successfully", [
                    'tokens_count' => count($tokens)
                ]);

                return [
                    'success' => true,
                    'tokens' => $tokens
                ];
            } elseif ($response && $response->hasError()) {
                $errorData = $response->getError();
                debugLog("API returned error", [
                    'error_type' => $errorData->getType(),
                    'error_message' => $errorData->getMessage()
                ]);

                return [
                    'success' => false,
                    'error_type' => $errorData->getType(),
                    'error_message' => $errorData->getMessage()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Empty response from server'
                ];
            }
        }

        // Handle specific gRPC error codes
        $errorMessage = 'Connection failed: ' . $grpcStatus->details;
        switch ($grpcStatus->code) {
            case 14: // UNAVAILABLE
                $errorMessage = 'Service unavailable. Please check your network connection and try again.';
                break;
            case 4: // DEADLINE_EXCEEDED
                $errorMessage = 'Request timeout. Please try again.';
                break;
            case 8: // RESOURCE_EXHAUSTED
                $errorMessage = 'Service temporarily overloaded. Please try again later.';
                break;
            case 16: // UNAUTHENTICATED
                $errorMessage = 'Authentication failed. Please check your API key.';
                break;
        }

        debugLog("gRPC call failed", [
            'status_code' => $grpcStatus->code,
            'error_message' => $errorMessage
        ]);

        return [
            'success' => false,
            'error' => $errorMessage,
            'status_code' => $grpcStatus->code
        ];
    }

    // Handle GET requests for testing
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'GetCardTokens API endpoint is working',
            'required_fields' => ['payer_reference'],
            'optional_fields' => [],
            'grpc_available' => class_exists('Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient')
        ]);
        exit;
    }

    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_end_clean();
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use POST.'
        ]);
        exit;
    }

    // Get JSON input
    $inputRaw = file_get_contents('php://input');
    $input = json_decode($inputRaw, true);

    if (!$input) {
        debugLog("JSON decode failed", ['error' => json_last_error_msg()]);
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON input: ' . json_last_error_msg()
        ]);
        exit;
    }

    // Validate required fields
    $requiredFields = ['payer_reference'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            debugLog("Missing required field: $field");
            ob_end_clean();
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Missing required field: $field"
            ]);
            exit;
        }
    }

    $result = getCardTokens($input, $config);

    ob_end_clean();
    echo json_encode($result);

} catch (Exception $e) {
    debugLog("Top-level Exception", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    debugLog("PHP Error", [
        'message' => $e->getMessage()
    ]);

    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'PHP Error: ' . $e->getMessage()
    ]);
}
?>
