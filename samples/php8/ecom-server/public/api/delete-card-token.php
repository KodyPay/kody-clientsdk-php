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
use Com\Kodypay\Grpc\Ecom\V1\DeleteCardTokenRequest;
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

    function deleteCardToken(array $requestData, array $config): array
    {

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
            $request = new DeleteCardTokenRequest();

            // Set store_id from config, not from request data
            $request->setStoreId($config['store_id']);

            // Set token_id - this is the only identifier we need
            if (empty($requestData['token_id'])) {
                throw new Exception("token_id is required");
            }

            $tokenId = trim($requestData['token_id']);
            if (empty($tokenId)) {
                throw new Exception("token_id cannot be empty");
            }

            $request->setTokenId($tokenId);


            // Try multiple connection attempts
            $maxRetries = 3;
            $retryDelay = 2; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    list($response, $grpcStatus) = $client->DeleteCardToken($request, $metadata)->wait();

                    // If successful or non-retryable error, break
                    // Retry on: 13 (INTERNAL), 14 (UNAVAILABLE), 4 (DEADLINE_EXCEEDED), 8 (RESOURCE_EXHAUSTED)
                    if ($grpcStatus->code === 0 || !in_array($grpcStatus->code, [13, 14, 4, 8])) {
                        break;
                    }

                    // If this was the last attempt, don't sleep
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                    }

                } catch (Exception $e) {
                    if ($attempt === $maxRetries) {
                        throw $e;
                    }
                    sleep($retryDelay);
                }
            }

            return processResponse($response, $grpcStatus);

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to delete card token: ' . $e->getMessage()
            ];
        }
    }

    function processResponse($response, $grpcStatus): array
    {
        if ($grpcStatus->code === 0) { // SUCCESS
            if ($response && $response->hasResponse()) {
                // Response is empty {} but that means success
                return [
                    'success' => true,
                    'message' => 'Card token deleted successfully'
                ];
            } elseif ($response && $response->hasError()) {
                $errorData = $response->getError();
                return [
                    'success' => false,
                    'error_type' => $errorData->getType(),
                    'error_message' => $errorData->getMessage()
                ];
            } else {
                // If no response and no error, but status is OK, it's still success
                return [
                    'success' => true,
                    'message' => 'Card token deleted successfully'
                ];
            }
        }

        // Handle specific gRPC error codes
        $errorMessage = 'Connection failed: ' . $grpcStatus->details;
        switch ($grpcStatus->code) {
            case 13: // INTERNAL
                $errorMessage = 'Internal service error. The delete operation may have failed due to a temporary service issue. Please try again in a few moments.';
                break;
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
            case 3: // INVALID_ARGUMENT
                $errorMessage = 'Invalid request parameters. Please check the token ID or reference.';
                break;
            case 5: // NOT_FOUND
                $errorMessage = 'Token not found. It may have already been deleted or does not exist.';
                break;
        }


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
            'message' => 'DeleteCardToken API endpoint is working',
            'required_fields' => ['token_id'],
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
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON input: ' . json_last_error_msg()
        ]);
        exit;
    }

    // Validate required fields - need token_id
    if (empty($input['token_id'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing required field: token_id'
        ]);
        exit;
    }

    $result = deleteCardToken($input, $config);

    ob_end_clean();
    echo json_encode($result);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'PHP Error: ' . $e->getMessage()
    ]);
}
?>
