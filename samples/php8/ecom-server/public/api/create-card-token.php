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
use Com\Kodypay\Grpc\Ecom\V1\CreateTokenRequest;
use Com\Kodypay\Grpc\Ecom\V1\RecurringProcessingModel;
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

    function createCardToken(array $requestData, array $config): array
    {
        debugLog("Creating card token", ['payer_reference' => $requestData['payer_reference']]);

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
            $request = new CreateTokenRequest();

            // Set store_id from config, not from request data
            $request->setStoreId($config['store_id']);

            // Set idempotency_uuid (use provided or generate)
            $idempotencyUuid = !empty($requestData['idempotency_uuid']) ? $requestData['idempotency_uuid'] : uniqid();
            $request->setIdempotencyUuid($idempotencyUuid);

            // Set required fields
            $request->setPayerReference($requestData['payer_reference']);
            $request->setReturnUrl($requestData['return_url']);

            // Set optional fields if provided
            if (!empty($requestData['token_reference'])) {
                $request->setTokenReference($requestData['token_reference']);
            }

            if (!empty($requestData['metadata'])) {
                $request->setMetadata($requestData['metadata']);
            }

            if (!empty($requestData['payer_statement'])) {
                $request->setPayerStatement($requestData['payer_statement']);
            }

            if (!empty($requestData['payer_email_address'])) {
                $request->setPayerEmailAddress($requestData['payer_email_address']);
            }

            if (!empty($requestData['payer_phone_number'])) {
                $request->setPayerPhoneNumber($requestData['payer_phone_number']);
            }

            // Set recurring processing model
            $recurringModel = RecurringProcessingModel::CARD_ON_FILE; // Default
            if (!empty($requestData['recurring_processing_model'])) {
                $modelString = strtoupper(trim($requestData['recurring_processing_model']));

                switch ($modelString) {
                    case 'SUBSCRIPTION':
                        $recurringModel = RecurringProcessingModel::SUBSCRIPTION;
                        break;
                    case 'UNSCHEDULED_CARD_ON_FILE':
                        $recurringModel = RecurringProcessingModel::UNSCHEDULED_CARD_ON_FILE;
                        break;
                    case 'CARD_ON_FILE':
                    default:
                        $recurringModel = RecurringProcessingModel::CARD_ON_FILE;
                        break;
                }
            }
            $request->setRecurringProcessingModel($recurringModel);

            // Try multiple connection attempts
            $maxRetries = 3;
            $retryDelay = 2; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    list($response, $grpcStatus) = $client->CreateCardToken($request, $metadata)->wait();

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
            debugLog("Exception in createCardToken", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create token: ' . $e->getMessage()
            ];
        }
    }

    function processResponse($response, $grpcStatus): array
    {
        if ($grpcStatus->code === 0) { // SUCCESS
            if ($response && $response->hasResponse()) {
                $responseData = $response->getResponse();
                debugLog("Token created successfully", [
                    'token_id' => $responseData->getTokenId()
                ]);

                return [
                    'success' => true,
                    'token_id' => $responseData->getTokenId(),
                    'create_token_url' => $responseData->getCreateTokenUrl()
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
            'message' => 'CreateCardToken API endpoint is working',
            'required_fields' => ['payer_reference', 'return_url'],
            'optional_fields' => ['idempotency_uuid', 'token_reference', 'metadata', 'payer_statement', 'payer_email_address', 'payer_phone_number', 'recurring_processing_model'],
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
    $requiredFields = ['payer_reference', 'return_url'];
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

    // Validate return_url format
    if (!filter_var($input['return_url'], FILTER_VALIDATE_URL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid return_url format'
        ]);
        exit;
    }

    // Validate email if provided
    if (!empty($input['payer_email_address']) && !filter_var($input['payer_email_address'], FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid email address format'
        ]);
        exit;
    }

    $result = createCardToken($input, $config);

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
