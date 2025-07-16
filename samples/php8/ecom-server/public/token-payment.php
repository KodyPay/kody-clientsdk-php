<?php
$config = require __DIR__ . '/config.php';

// Sanitize error message if present
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

// Check for token creation success
$tokenCreated = isset($_GET['token_created']) && $_GET['token_created'] === '1';

// Generate random values
function generateRandomDigits($length = 10) {
    $digits = '';
    for ($i = 0; $i < $length; $i++) {
        $digits .= rand(0, 9);
    }
    return $digits;
}

// Generate proper UUID v4 format
function generateUuidV4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$randomIdempotencyUuid = generateUuidV4();
$randomTokenReference = 'test-token-' . generateRandomDigits(10);

// Auto-generate return URL
$returnUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/token-payment-callback.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - Token Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .top-nav {
            text-align: right;
            margin-bottom: 20px;
        }

        .top-nav a {
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }

        .top-nav a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }

        .required::after {
            content: " *";
            color: red;
        }

        input[type="number"],
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
        }

        form button,
        button[type="submit"],
        .regenerate-button,
        .redirect-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        form button:hover,
        button[type="submit"]:hover,
        .regenerate-button:hover,
        .redirect-button:hover {
            background-color: #45a049;
        }

        form button:disabled,
        button[type="submit"]:disabled,
        .regenerate-button:disabled,
        .redirect-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .links {
            text-align: center;
            margin: 20px 0;
        }

        .links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            padding: 20px;
            border: 1px solid #ffcccc;
            background-color: #fff8f8;
            margin: 20px 0;
            border-radius: 6px;
        }

        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .token-result {
            background-color: #e7f3ff;
            border: 1px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .token-result h3 {
            color: #007bff;
            margin-top: 0;
        }

        .countdown {
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }

        .token-result .redirect-button {
            background-color: #007bff;
            margin-top: 15px;
            padding: 12px 24px;
        }

        .token-result .redirect-button:hover {
            background-color: #0056b3;
        }

        .field-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: -15px;
            margin-bottom: 15px;
        }

        form .regenerate-button {
            width: auto;
            margin-bottom: 20px;
            background-color: #6c757d;
            padding: 8px 16px;
            font-size: 14px;
        }

        form .regenerate-button:hover {
            background-color: #5a6268;
        }

        .dev-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin-top: 30px;
        }

        .dev-info h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .dev-info ul {
            line-height: 1.6;
            color: #555;
        }

        .dev-info li {
            margin-bottom: 5px;
        }

        .dev-info .nested-list {
            list-style-type: disc;
            margin-left: 20px;
        }

        .dev-info pre {
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
        }

        .dev-info code {
            background-color: #f1f3f4;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

    </style>
    <link rel="stylesheet" href="css/sdk-common.php">
    <script src="js/sdk-common.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Token Payment</h1>

        <?php if ($errorMessage): ?>
            <div class="error-message">Error: <?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if ($tokenCreated): ?>
            <div class="success-message">
                <strong>Success!</strong> Your payment token has been created successfully. You can now use this token for future payments.
            </div>
        <?php endif; ?>

        <h2>Step 1: Create Payment Token</h2>
        <p>Before you can make payments with saved cards, you need to create a payment token. This process will redirect the customer to a secure tokenization page.</p>

        <form id="createTokenForm">
            <label for="payer_reference" class="required">Customer ID (Payer Reference)</label>
            <input type="text" id="payer_reference" name="payer_reference" placeholder="e.g., customer_123" required>

            <label for="idempotency_uuid">Idempotency UUID:</label>
            <input type="text" id="idempotency_uuid" name="idempotency_uuid" value="<?php echo htmlspecialchars($randomIdempotencyUuid); ?>" readonly>
            <div class="field-info">Auto-generated UUID v4 format</div>

            <label for="token_reference">Token Reference:</label>
            <input type="text" id="token_reference" name="token_reference" value="<?php echo htmlspecialchars($randomTokenReference); ?>" readonly>
            <div class="field-info">Auto-generated token reference</div>

            <!-- Hidden return URL field -->
            <input type="hidden" id="return_url" name="return_url" value="<?php echo htmlspecialchars($returnUrl); ?>">

            <button type="button" id="regenerateButton" class="regenerate-button">🔄 Generate New IDs</button>

            <label for="payer_email_address">Customer Email (optional):</label>
            <input type="email" id="payer_email_address" name="payer_email_address" placeholder="customer@example.com">

            <label for="payer_phone_number">Customer Phone (optional):</label>
            <input type="tel" id="payer_phone_number" name="payer_phone_number" placeholder="+1234567890">

            <label for="payer_statement">Payer Statement (optional):</label>
            <input type="text" id="payer_statement" name="payer_statement" placeholder="Statement text (max 22 chars)">

            <label for="recurring_processing_model" class="required">Recurring Processing Model</label>
            <select id="recurring_processing_model" name="recurring_processing_model" required>
                <option value="CARD_ON_FILE">Card on File</option>
                <option value="SUBSCRIPTION" selected>Subscription</option>
                <option value="UNSCHEDULED_CARD_ON_FILE">Unscheduled Card on File</option>
            </select>

            <label for="metadata">Metadata (optional):</label>
            <textarea id="metadata" name="metadata" rows="3" placeholder='{"customer_name": "John Doe", "card_alias": "My Primary Card"}'></textarea>

            <button type="submit" id="createTokenButton">Create Payment Token</button>
        </form>

        <div id="tokenResult" class="token-result" style="display: none;">
            <h3>Token Created Successfully!</h3>
            <p>Token ID: <strong><span id="tokenId"></span></strong></p>
            <p>You will now be redirected to complete the tokenization process.</p>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
            <a href="/checkout.php">Standard Payment</a>
        </div>

        <div class="dev-info">
            <h2>Developer Information</h2>
            <p>This page demonstrates token-based payment creation. Tokens securely store payment methods for future use without exposing card details.</p>

            <h3>Key Points</h3>
            <ul>
                <li><strong>CreateCardToken API:</strong> Creates secure payment tokens via <code>/api/create-card-token.php</code></li>
                <li><strong>Required:</strong> payer_reference, return_url, recurring_processing_model</li>
                <li><strong>Flow:</strong> Create token → Customer authorization → Token ready for payments</li>
            </ul>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Create Card Token</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Method:</strong> <code>CreateCardToken()</code></p>
                <p><strong>Request:</strong> <code>CreateTokenRequest</code></p>
                <p><strong>Response:</strong> <code>CreateTokenResponse</code></p>
            </div>

            <div class="code-section">
                <h3>SDK Examples</h3>

                <div class="tabs">
                    <button class="tab-button" onclick="showTab('php')">PHP</button>
                    <button class="tab-button" onclick="showTab('java')">Java</button>
                    <button class="tab-button" onclick="showTab('python')">Python</button>
                    <button class="tab-button" onclick="showTab('dotnet')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('php-code')">Copy</button>
                        <pre id="php-code"><code>&lt;?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\CreateTokenRequest;
use Grpc\ChannelCredentials;

// Configuration
$HOSTNAME = "grpc-staging.kodypay.com";
$API_KEY = "your-api-key";

// Step 1: Initialize SDK client with SSL credentials
$client = new KodyEcomPaymentsServiceClient($HOSTNAME, [
    'credentials' => ChannelCredentials::createSsl()
]);

// Step 2: Set authentication headers with your API key
$metadata = ['X-API-Key' => [$API_KEY]];

// Step 3: Create CreateTokenRequest and set required fields
$request = new CreateTokenRequest();
$request->setStoreId('your-store-id');
$request->setPayerReference('customer_123'); // Customer ID
$request->setReturnUrl('https://your-domain.com/token-callback');
$request->setRecurringProcessingModel('SUBSCRIPTION');

// Step 4: Set optional fields
$request->setTokenReference('token-ref-' . uniqid());
$request->setIdempotencyUuid(uniqid('', true)); // or use: bin2hex(random_bytes(16))
$request->setPayerEmailAddress('customer@example.com');
$request->setPayerPhoneNumber('+1234567890');
$request->setPayerStatement('Card Setup');
$request->setMetadata('{"customer_name": "John Doe"}');

// Step 5: Call CreateCardToken() method and wait for response
list($response, $status) = $client->CreateCardToken($request, $metadata)->wait();

// Step 6: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 7: Process response
if ($response->hasResponse()) {
    $responseData = $response->getResponse();
    echo "Token ID: " . $responseData->getTokenId() . PHP_EOL;
    echo "Token URL: " . $responseData->getCreateTokenUrl() . PHP_EOL;

    // Redirect customer to complete tokenization
    header('Location: ' . $responseData->getCreateTokenUrl());
} else if ($response->hasError()) {
    $error = $response->getError();
    echo "API Error: " . $error->getMessage() . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
import com.kodypay.grpc.ecom.v1.CreateTokenRequest;
import com.kodypay.grpc.ecom.v1.CreateTokenResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;
import java.util.UUID;

public class CreateCardTokenExample {
    public static final String HOSTNAME = "grpc-staging.kodypay.com";
    public static final String API_KEY = "your-api-key";

    public static void main(String[] args) {
        // Step 1: Create metadata with API key
        Metadata metadata = new Metadata();
        metadata.put(Metadata.Key.of("X-API-Key", Metadata.ASCII_STRING_MARSHALLER), API_KEY);

        // Step 2: Build secure channel and create client
        var channel = ManagedChannelBuilder.forAddress(HOSTNAME, 443)
            .useTransportSecurity()
            .build();
        var client = KodyEcomPaymentsServiceGrpc.newBlockingStub(channel)
            .withInterceptors(MetadataUtils.newAttachHeadersInterceptor(metadata));

        // Step 3: Create CreateTokenRequest and set required fields
        CreateTokenRequest request = CreateTokenRequest.newBuilder()
            .setStoreId("your-store-id")
            .setPayerReference("customer_123") // Customer ID
            .setReturnUrl("https://your-domain.com/token-callback")
            .setRecurringProcessingModel("SUBSCRIPTION")
            .setTokenReference("token-ref-" + System.currentTimeMillis())
            .setIdempotencyUuid(UUID.randomUUID().toString())
            .setPayerEmailAddress("customer@example.com")
            .setPayerPhoneNumber("+1234567890")
            .setPayerStatement("Card Setup")
            .setMetadata("{\"customer_name\": \"John Doe\"}")
            .build();

        // Step 4: Call CreateCardToken() method and get response
        CreateTokenResponse response = client.createCardToken(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            var responseData = response.getResponse();
            System.out.println("Token ID: " + responseData.getTokenId());
            System.out.println("Token URL: " + responseData.getCreateTokenUrl());

            // Redirect customer to complete tokenization
            // response.sendRedirect(responseData.getCreateTokenUrl());
        } else if (response.hasError()) {
            var error = response.getError();
            System.out.println("API Error: " + error.getMessage());
        }
    }
}</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div id="python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('python-code')">Copy</button>
                        <pre id="python-code"><code>import grpc
import uuid
import kody_clientsdk_python.ecom.v1.ecom_pb2 as kody_model
import kody_clientsdk_python.ecom.v1.ecom_pb2_grpc as kody_client

def create_card_token():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create CreateTokenRequest and set required fields
    request = kody_model.CreateTokenRequest(
        store_id="your-store-id",
        payer_reference="customer_123",  # Customer ID
        return_url="https://your-domain.com/token-callback",
        recurring_processing_model="SUBSCRIPTION",
        token_reference=f"token-ref-{int(time.time())}",
        idempotency_uuid=str(uuid.uuid4()),
        payer_email_address="customer@example.com",
        payer_phone_number="+1234567890",
        payer_statement="Card Setup",
        metadata='{"customer_name": "John Doe"}'
    )

    # Step 4: Call CreateCardToken() method and get response
    response = client.CreateCardToken(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        response_data = response.response
        print(f"Token ID: {response_data.token_id}")
        print(f"Token URL: {response_data.create_token_url}")

        # Redirect customer to complete tokenization
        # webbrowser.open(response_data.create_token_url)
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    create_card_token()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('dotnet-code')">Copy</button>
                        <pre id="dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Ecom.V1;
using System;

class Program
{
    static async Task Main(string[] args)
    {
        // Configuration
        var HOSTNAME = "grpc-staging.kodypay.com";
        var API_KEY = "your-api-key";

        // Step 1: Create secure channel
        var channel = GrpcChannel.ForAddress("https://" + HOSTNAME);

        // Step 2: Create client
        var client = new KodyEcomPaymentsService.KodyEcomPaymentsServiceClient(channel);

        // Step 3: Set authentication headers with API key
        var metadata = new Metadata
        {
            { "X-API-Key", API_KEY }
        };

        // Step 4: Create CreateTokenRequest and set required fields
        var request = new CreateTokenRequest
        {
            StoreId = "your-store-id",
            PayerReference = "customer_123", // Customer ID
            ReturnUrl = "https://your-domain.com/token-callback",
            RecurringProcessingModel = "SUBSCRIPTION",
            TokenReference = $"token-ref-{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}",
            IdempotencyUuid = Guid.NewGuid().ToString(),
            PayerEmailAddress = "customer@example.com",
            PayerPhoneNumber = "+1234567890",
            PayerStatement = "Card Setup",
            Metadata = "{\"customer_name\": \"John Doe\"}"
        };

        try
        {
            // Step 5: Call CreateCardToken() method and get response
            var response = await client.CreateCardTokenAsync(request, metadata);

            // Step 6: Process response
            if (response.ResponseCase == CreateTokenResponse.ResponseOneofCase.Response)
            {
                var responseData = response.Response;
                Console.WriteLine($"Token ID: {responseData.TokenId}");
                Console.WriteLine($"Token URL: {responseData.CreateTokenUrl}");

                // Redirect customer to complete tokenization
                // Response.Redirect(responseData.CreateTokenUrl);
            }
            else if (response.ResponseCase == CreateTokenResponse.ResponseOneofCase.Error)
            {
                var error = response.Error;
                Console.WriteLine($"API Error: {error.Message}");
            }
        }
        catch (RpcException ex)
        {
            Console.WriteLine($"gRPC Error: {ex.Status.StatusCode} - {ex.Status.Detail}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Exception: {ex.Message}");
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createTokenForm = document.getElementById('createTokenForm');
            const createTokenButton = document.getElementById('createTokenButton');
            const tokenResult = document.getElementById('tokenResult');
            const tokenId = document.getElementById('tokenId');
            const regenerateButton = document.getElementById('regenerateButton');

            // Function to generate proper UUID v4
            function generateUuidV4() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }

            // Function to generate random digits
            function generateRandomDigits(length = 10) {
                let digits = '';
                for (let i = 0; i < length; i++) {
                    digits += Math.floor(Math.random() * 10);
                }
                return digits;
            }

            // Function to regenerate random values
            function regenerateRandomValues() {
                // Generate new idempotency UUID
                const newIdempotencyUuid = generateUuidV4();
                document.getElementById('idempotency_uuid').value = newIdempotencyUuid;

                // Generate new token reference
                const newTokenReference = 'test-token-' + generateRandomDigits(10);
                document.getElementById('token_reference').value = newTokenReference;
            }

            // Add event listener to regenerate button
            regenerateButton.addEventListener('click', regenerateRandomValues);


            createTokenForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Disable button and show loading state
                createTokenButton.disabled = true;
                createTokenButton.textContent = 'Creating Token...';

                // Collect ALL form data (including hidden fields)
                const formData = new FormData(createTokenForm);
                const requestData = {};

                // Get all form fields, only include non-empty values
                for (let [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        requestData[key] = value.trim();
                    }
                }

                console.log('Sending request:', requestData);

                try {
                    const response = await fetch('/api/create-card-token.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    console.log('Received response:', result);

                    // Handle new response format
                    if (result.response && result.response.token_id && result.response.create_token_url) {
                        // Success case - new format
                        tokenId.textContent = result.response.token_id;
                        tokenResult.style.display = 'block';
                        createTokenForm.style.display = 'none';

                        // Countdown and redirect logic
                        let countdown = 3;
                        const countdownElement = document.createElement('div');
                        countdownElement.className = 'countdown';
                        countdownElement.innerHTML = `Redirecting in ${countdown} seconds...`;
                        tokenResult.appendChild(countdownElement);

                        const countdownInterval = setInterval(() => {
                            countdown--;
                            countdownElement.innerHTML = `Redirecting in ${countdown} seconds...`;

                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = result.response.create_token_url;
                            }
                        }, 1000);

                        const redirectButton = document.createElement('button');
                        redirectButton.textContent = 'Go to Token Creation Page Now';
                        redirectButton.className = 'redirect-button';
                        redirectButton.onclick = function() {
                            clearInterval(countdownInterval);
                            window.location.href = result.response.create_token_url;
                        };
                        tokenResult.appendChild(redirectButton);

                    } else if (result.success && result.token_id && result.create_token_url) {
                        // Legacy success format (fallback)
                        tokenId.textContent = result.token_id;
                        tokenResult.style.display = 'block';
                        createTokenForm.style.display = 'none';

                        // Same redirect logic as above
                        let countdown = 3;
                        const countdownElement = document.createElement('div');
                        countdownElement.className = 'countdown';
                        countdownElement.innerHTML = `Redirecting in ${countdown} seconds...`;
                        tokenResult.appendChild(countdownElement);

                        const countdownInterval = setInterval(() => {
                            countdown--;
                            countdownElement.innerHTML = `Redirecting in ${countdown} seconds...`;

                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = result.create_token_url;
                            }
                        }, 1000);

                        const redirectButton = document.createElement('button');
                        redirectButton.textContent = 'Go to Token Creation Page Now';
                        redirectButton.className = 'redirect-button';
                        redirectButton.onclick = function() {
                            clearInterval(countdownInterval);
                            window.location.href = result.create_token_url;
                        };
                        tokenResult.appendChild(redirectButton);

                    } else {
                        // Error case
                        let errorMessage = 'Unknown error occurred';

                        if (result.error_message) {
                            errorMessage = result.error_message;
                        } else if (result.error) {
                            errorMessage = result.error;
                        } else if (result.success === false) {
                            errorMessage = 'Token creation failed';
                        }

                        alert('Error creating token: ' + errorMessage);
                        console.error('Error details:', result);
                    }
                } catch (error) {
                    console.error('Network/Parse Error:', error);
                    alert('Error creating token: ' + error.message);
                } finally {
                    // Re-enable button
                    createTokenButton.disabled = false;
                    createTokenButton.textContent = 'Create Payment Token';
                }
            });
        });

    </script>
</body>
</html>
