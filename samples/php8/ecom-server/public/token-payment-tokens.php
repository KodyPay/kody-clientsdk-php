<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Tokens</title>
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

        .payer-input {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .payer-input label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
        }

        .payer-input input {
            width: 300px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }

        .payer-input button {
            margin-left: 10px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .payer-input button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #fafafa;
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 40px 0;
            flex-direction: column;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: red;
            padding: 20px;
            border: 1px solid #ffcccc;
            background-color: #fff8f8;
            margin: 20px 0;
            border-radius: 6px;
        }

        .no-tokens {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }

        .status-ready {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }

        .status-failed {
            color: #e74c3c;
            font-weight: bold;
        }

        .status-deleted {
            color: #9e9e9e;
            font-weight: bold;
        }

        .payment-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .payment-btn:hover {
            background-color: #45a049;
        }

        .payment-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .delete-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .button-group {
            display: flex;
            gap: 5px;
            align-items: center;
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

        /* Developer Section Styles */
        .developer-section {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .developer-section h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .code-section {
            margin: 30px 0;
        }

        .code-section h3 {
            color: #555;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .tab-button {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            padding: 8px 16px;
            cursor: pointer;
            margin-right: 4px;
            border-radius: 4px 4px 0 0;
            color: #555;
            font-size: 14px;
            display: inline-block;
        }

        .tab-button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .code-block {
            position: relative;
            background: #2d3748;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .code-block pre {
            margin: 0;
            padding: 20px;
            color: #e2e8f0;
            background: #2d3748;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            z-index: 10;
            transition: background-color 0.2s;
        }

        .copy-btn:hover {
            background: #0056b3;
        }

        .copy-btn.copied {
            background: #28a745;
        }

        .sdk-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }

        .sdk-info h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }

        .sdk-info p {
            margin: 5px 0;
            color: #555;
        }

        .section-divider {
            border-top: 1px solid #ddd;
            margin: 40px 0;
        }

        .card-info {
            font-size: 12px;
            color: #666;
        }

        .token-reference {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }
    </style>
    <script src="js/bubble.php"></script>
    <script>
        let currentPayerReference = '';

        function fetchTokens() {
            const payerReference = document.getElementById('payer-reference').value.trim();

            if (!payerReference) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Please enter a payer reference to view tokens</div>';
                return;
            }

            currentPayerReference = payerReference;
            const container = document.getElementById('tokens-container');

            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Loading tokens...</span>
                </div>`;

            fetch('api/get-card-tokens.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payer_reference: payerReference
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to fetch tokens');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to fetch tokens');
                    }

                    if (!data.tokens || data.tokens.length === 0) {
                        container.innerHTML = '<div class="no-tokens">No tokens found for this payer reference</div>';
                        return;
                    }

                    // Build tokens table
                    let html = `
                        <table id="tokens-table">
                            <thead>
                                <tr>
                                    <th>Token ID</th>
                                    <th>Payment Token</th>
                                    <th>Card Info</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Token Reference</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.tokens.forEach(token => {
                        const status = getStatusText(token.status);
                        const statusClass = getStatusClass(token.status);
                        const createdDate = token.created_at ? new Date(token.created_at).toLocaleString() : 'N/A';
                        const cardInfo = `${getPaymentMethodText(token.payment_method)} **** ${token.card_last_4_digits}`;
                        const tokenReference = token.token_reference || 'N/A';

                        // Show appropriate buttons based on token status
                        let actionButtons = '<div class="button-group">';

                        // Payment button - only available for READY tokens
                        if (token.status === 2) { // READY status
                            actionButtons += `<button onclick="useTokenForPayment('${token.payment_token}')" class="payment-btn">Use for Payment</button>`;
                        } else {
                            actionButtons += '<span class="card-info">Not available</span>';
                        }

                        // Delete button - available for READY, PENDING, and FAILED tokens (not DELETED or PENDING_DELETE)
                        if (token.status !== 3 && token.status !== 4) { // Not DELETED or PENDING_DELETE
                            actionButtons += `<button onclick="deleteToken('${token.token_id}')" class="delete-btn">Delete</button>`;
                        }

                        actionButtons += '</div>';
                        const actionButton = actionButtons;

                        html += `
                            <tr>
                                <td>${token.token_id}</td>
                                <td class="card-info">${token.payment_token}</td>
                                <td>${cardInfo}</td>
                                <td class="${statusClass}">${status}</td>
                                <td class="card-info">${createdDate}</td>
                                <td class="token-reference">${tokenReference}</td>
                                <td>${actionButton}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching tokens:', error);
                    container.innerHTML = `<div class="error-message">${escapeHtml(error.message)}</div>`;
                });
        }

        function getStatusText(status) {
            switch(status) {
                case 0: return 'Pending';
                case 1: return 'Failed';
                case 2: return 'Ready';
                case 3: return 'Deleted';
                case 4: return 'Pending Delete';
                default: return 'Unknown';
            }
        }

        function getStatusClass(status) {
            switch(status) {
                case 0: return 'status-pending';
                case 1: return 'status-failed';
                case 2: return 'status-ready';
                case 3: return 'status-deleted';
                case 4: return 'status-deleted';
                default: return '';
            }
        }

        function getPaymentMethodText(method) {
            switch(method) {
                case 0: return 'Visa';
                case 1: return 'Mastercard';
                case 2: return 'Amex';
                case 3: return 'Bancontact';
                case 4: return 'China UnionPay';
                case 5: return 'Maestro';
                case 6: return 'Diners';
                case 7: return 'Discover';
                case 8: return 'JCB';
                case 9: return 'Alipay';
                case 10: return 'WeChat';
                default: return 'Unknown';
            }
        }

        function useTokenForPayment(paymentToken) {
            // Redirect to token payment page with the selected token
            window.location.href = `token-payment-tokens.php?payment_token=${encodeURIComponent(paymentToken)}`;
        }

        function deleteToken(tokenId) {
            if (!confirm('Are you sure you want to delete this token? This action cannot be undone.')) {
                return;
            }

            // Disable the delete button to prevent multiple clicks
            const deleteButtons = document.querySelectorAll(`button[onclick="deleteToken('${tokenId}')"]`);
            deleteButtons.forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Deleting...';
            });

            fetch('api/delete-card-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token_id: tokenId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to delete token');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to delete token');
                    }

                    // Success - refresh the tokens list
                    alert('Token deleted successfully');
                    fetchTokens();
                })
                .catch(error => {
                    console.error('Error deleting token:', error);
                    alert('Error deleting token: ' + error.message);

                    // Re-enable the button on error
                    deleteButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.textContent = 'Delete';
                    });
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function onPayerReferenceChange() {
            // Clear current tokens when payer reference changes
            if (currentPayerReference !== document.getElementById('payer-reference').value.trim()) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Click "Load Tokens" or press Enter to view tokens for this payer</div>';
                currentPayerReference = '';
            }
        }

        function handleKeyPress(event) {
            // Check if Enter key was pressed
            if (event.key === 'Enter' || event.keyCode === 13) {
                event.preventDefault(); // Prevent form submission if inside a form
                fetchTokens();
            }
        }

        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            const code = codeElement.textContent || codeElement.innerText;

            navigator.clipboard.writeText(code).then(function() {
                // Visual feedback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = code;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Visual feedback for fallback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            });
        }

        function showTab(language, section) {
            // Hide all tab contents for this section
            const tabContents = document.querySelectorAll(`#${section}-section .tab-content`);
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons in this section
            const tabButtons = document.querySelectorAll(`#${section}-section .tab-button`);
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            const selectedContent = document.getElementById(`${section}-${language}-content`);
            if (selectedContent) {
                selectedContent.classList.add('active');
            }

            // Add active class to clicked button
            const selectedButton = document.querySelector(`#${section}-section [onclick="showTab('${language}', '${section}')"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Card Tokens</h1>

        <div class="payer-input">
            <label for="payer-reference">Payer Reference:</label>
            <input type="text" id="payer-reference" placeholder="Enter payer reference (e.g., user123)" onchange="onPayerReferenceChange()" onkeypress="handleKeyPress(event)">
            <button onclick="fetchTokens()">Load Tokens</button>
        </div>

        <div id="tokens-container">
            <div class="no-tokens">Please enter a payer reference and click "Load Tokens" or press Enter to view tokens</div>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Token Management</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Methods:</strong> <code>GetCardTokens()</code>, <code>DeleteCardToken()</code></p>
                <p><strong>Requests:</strong> <code>GetCardTokensRequest</code>, <code>DeleteCardTokenRequest</code></p>
                <p><strong>Responses:</strong> <code>GetCardTokensResponse</code>, <code>DeleteCardTokenResponse</code></p>
            </div>

            <div class="code-section" id="get-tokens-section">
                <h3>Get Card Tokens - SDK Examples</h3>

                <div class="tabs">
                    <button class="tab-button" onclick="showTab('php', 'get-tokens')">PHP</button>
                    <button class="tab-button" onclick="showTab('java', 'get-tokens')">Java</button>
                    <button class="tab-button" onclick="showTab('python', 'get-tokens')">Python</button>
                    <button class="tab-button" onclick="showTab('dotnet', 'get-tokens')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="get-tokens-php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('get-tokens-php-code')">Copy</button>
                        <pre id="get-tokens-php-code"><code>&lt;?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\GetCardTokensRequest;
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

// Step 3: Create GetCardTokensRequest and set required fields
$request = new GetCardTokensRequest();
$request->setStoreId('your-store-id');
$request->setPayerReference('user123'); // The payer for whom to list tokens

// Step 4: Call GetCardTokens() method and wait for response
list($response, $status) = $client->GetCardTokens($request, $metadata)->wait();

// Step 5: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 6: Process response
if ($response->hasResponse()) {
    $responseData = $response->getResponse();
    $tokens = $responseData->getTokens();

    echo "Found " . count($tokens) . " tokens:" . PHP_EOL;
    foreach ($tokens as $token) {
        echo "Token ID: " . $token->getTokenId() . PHP_EOL;
        echo "Payment Token: " . $token->getPaymentToken() . PHP_EOL;
        echo "Status: " . $token->getStatus() . PHP_EOL;
        echo "Card: " . $token->getPaymentMethod() . " **** " . $token->getCardLast4Digits() . PHP_EOL;
        echo "---" . PHP_EOL;
    }
} else if ($response->hasError()) {
    $error = $response->getError();
    echo "API Error: " . $error->getMessage() . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="get-tokens-java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('get-tokens-java-code')">Copy</button>
                        <pre id="get-tokens-java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
import com.kodypay.grpc.ecom.v1.GetCardTokensRequest;
import com.kodypay.grpc.ecom.v1.GetCardTokensResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class GetCardTokensExample {
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

        // Step 3: Create GetCardTokensRequest and set required fields
        GetCardTokensRequest request = GetCardTokensRequest.newBuilder()
            .setStoreId("your-store-id")
            .setPayerReference("user123") // The payer for whom to list tokens
            .build();

        // Step 4: Call GetCardTokens() method and get response
        GetCardTokensResponse response = client.getCardTokens(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            var responseData = response.getResponse();
            System.out.println("Found " + responseData.getTokensCount() + " tokens:");

            responseData.getTokensList().forEach(token -> {
                System.out.println("Token ID: " + token.getTokenId());
                System.out.println("Payment Token: " + token.getPaymentToken());
                System.out.println("Status: " + token.getStatus());
                System.out.println("Card: " + token.getPaymentMethod() + " **** " + token.getCardLast4Digits());
                System.out.println("---");
            });
        } else if (response.hasError()) {
            var error = response.getError();
            System.out.println("API Error: " + error.getMessage());
        }
    }
}</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div id="get-tokens-python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('get-tokens-python-code')">Copy</button>
                        <pre id="get-tokens-python-code"><code>import grpc
import kody_clientsdk_python.ecom.v1.ecom_pb2 as kody_model
import kody_clientsdk_python.ecom.v1.ecom_pb2_grpc as kody_client

def get_card_tokens():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create GetCardTokensRequest and set required fields
    request = kody_model.GetCardTokensRequest(
        store_id="your-store-id",
        payer_reference="user123"  # The payer for whom to list tokens
    )

    # Step 4: Call GetCardTokens() method and get response
    response = client.GetCardTokens(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        response_data = response.response
        print(f"Found {len(response_data.tokens)} tokens:")

        for token in response_data.tokens:
            print(f"Token ID: {token.token_id}")
            print(f"Payment Token: {token.payment_token}")
            print(f"Status: {token.status}")
            print(f"Card: {token.payment_method} **** {token.card_last_4_digits}")
            print("---")
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    get_card_tokens()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="get-tokens-dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('get-tokens-dotnet-code')">Copy</button>
                        <pre id="get-tokens-dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Ecom.V1;

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

        // Step 4: Create GetCardTokensRequest and set required fields
        var request = new GetCardTokensRequest
        {
            StoreId = "your-store-id",
            PayerReference = "user123" // The payer for whom to list tokens
        };

        // Step 5: Call GetCardTokens() method and get response
        var response = await client.GetCardTokensAsync(request, metadata);

        // Step 6: Process response
        if (response.ResponseCase == GetCardTokensResponse.ResponseOneofCase.Response)
        {
            var responseData = response.Response;
            Console.WriteLine($"Found {responseData.Tokens.Count} tokens:");

            foreach (var token in responseData.Tokens)
            {
                Console.WriteLine($"Token ID: {token.TokenId}");
                Console.WriteLine($"Payment Token: {token.PaymentToken}");
                Console.WriteLine($"Status: {token.Status}");
                Console.WriteLine($"Card: {token.PaymentMethod} **** {token.CardLast4Digits}");
                Console.WriteLine("---");
            }
        }
        else if (response.ResponseCase == GetCardTokensResponse.ResponseOneofCase.Error)
        {
            var error = response.Error;
            Console.WriteLine($"API Error: {error.Message}");
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>

            <div class="code-section" id="delete-token-section">
                <h3>Delete Card Token - SDK Examples</h3>

                <div class="tabs">
                    <button class="tab-button" onclick="showTab('php', 'delete-token')">PHP</button>
                    <button class="tab-button" onclick="showTab('java', 'delete-token')">Java</button>
                    <button class="tab-button" onclick="showTab('python', 'delete-token')">Python</button>
                    <button class="tab-button" onclick="showTab('dotnet', 'delete-token')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="delete-token-php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-token-php-code')">Copy</button>
                        <pre id="delete-token-php-code"><code>&lt;?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\DeleteCardTokenRequest;
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

// Step 3: Create DeleteCardTokenRequest and set required fields
$request = new DeleteCardTokenRequest();
$request->setStoreId('your-store-id');
$request->setTokenId('token-id-to-delete'); // or use setTokenReference()

// Step 4: Call DeleteCardToken() method and wait for response
list($response, $status) = $client->DeleteCardToken($request, $metadata)->wait();

// Step 5: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 6: Process response
if ($response->hasResponse()) {
    echo "Token deleted successfully!" . PHP_EOL;
} else if ($response->hasError()) {
    $error = $response->getError();
    echo "API Error: " . $error->getMessage() . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="delete-token-java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-token-java-code')">Copy</button>
                        <pre id="delete-token-java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
import com.kodypay.grpc.ecom.v1.DeleteCardTokenRequest;
import com.kodypay.grpc.ecom.v1.DeleteCardTokenResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class DeleteCardTokenExample {
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

        // Step 3: Create DeleteCardTokenRequest and set required fields
        DeleteCardTokenRequest request = DeleteCardTokenRequest.newBuilder()
            .setStoreId("your-store-id")
            .setTokenId("token-id-to-delete") // or use setTokenReference()
            .build();

        // Step 4: Call DeleteCardToken() method and get response
        DeleteCardTokenResponse response = client.deleteCardToken(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            System.out.println("Token deleted successfully!");
        } else if (response.hasError()) {
            var error = response.getError();
            System.out.println("API Error: " + error.getMessage());
        }
    }
}</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div id="delete-token-python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-token-python-code')">Copy</button>
                        <pre id="delete-token-python-code"><code>import grpc
import kody_clientsdk_python.ecom.v1.ecom_pb2 as kody_model
import kody_clientsdk_python.ecom.v1.ecom_pb2_grpc as kody_client

def delete_card_token():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create DeleteCardTokenRequest and set required fields
    request = kody_model.DeleteCardTokenRequest(
        store_id="your-store-id",
        token_id="token-id-to-delete"  # or use token_reference
    )

    # Step 4: Call DeleteCardToken() method and get response
    response = client.DeleteCardToken(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        print("Token deleted successfully!")
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    delete_card_token()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="delete-token-dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-token-dotnet-code')">Copy</button>
                        <pre id="delete-token-dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Ecom.V1;

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

        // Step 4: Create DeleteCardTokenRequest and set required fields
        var request = new DeleteCardTokenRequest
        {
            StoreId = "your-store-id",
            TokenId = "token-id-to-delete" // or use TokenReference
        };

        // Step 5: Call DeleteCardToken() method and get response
        var response = await client.DeleteCardTokenAsync(request, metadata);

        // Step 6: Process response
        if (response.ResponseCase == DeleteCardTokenResponse.ResponseOneofCase.Response)
        {
            Console.WriteLine("Token deleted successfully!");
        }
        else if (response.ResponseCase == DeleteCardTokenResponse.ResponseOneofCase.Error)
        {
            var error = response.Error;
            Console.WriteLine($"API Error: {error.Message}");
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isFirstLoad = true;

        function fetchTokens() {
            const payerReference = document.getElementById('payer-reference').value.trim();

            if (!payerReference) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Please enter a payer reference to view tokens</div>';
                return;
            }

            // Allow reloading even if same payer reference (for manual refresh)

            currentPayerReference = payerReference;
            const container = document.getElementById('tokens-container');

            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Loading tokens...</span>
                </div>`;

            fetch('api/get-card-tokens.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payer_reference: payerReference
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to fetch tokens');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to fetch tokens');
                    }

                    if (!data.tokens || data.tokens.length === 0) {
                        container.innerHTML = '<div class="no-tokens">No tokens found for this payer reference</div>';
                        return;
                    }

                    // Build tokens table
                    let html = `
                        <table id="tokens-table">
                            <thead>
                                <tr>
                                    <th>Token ID</th>
                                    <th>Payment Token</th>
                                    <th>Card Info</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Token Reference</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.tokens.forEach(token => {
                        const status = getStatusText(token.status);
                        const statusClass = getStatusClass(token.status);
                        const createdDate = token.created_at ? new Date(token.created_at).toLocaleString() : 'N/A';
                        const cardInfo = `${getPaymentMethodText(token.payment_method)} **** ${token.card_last_4_digits}`;
                        const tokenReference = token.token_reference || 'N/A';

                        // Show appropriate buttons based on token status
                        let actionButtons = '<div class="button-group">';

                        // Payment button - only available for READY tokens
                        if (token.status === 2) { // READY status
                            actionButtons += `<button onclick="useTokenForPayment('${token.payment_token}')" class="payment-btn">Use for Payment</button>`;
                        } else {
                            actionButtons += '<span class="card-info">Not available</span>';
                        }

                        // Delete button - available for READY, PENDING, and FAILED tokens (not DELETED or PENDING_DELETE)
                        if (token.status !== 3 && token.status !== 4) { // Not DELETED or PENDING_DELETE
                            actionButtons += `<button onclick="deleteToken('${token.token_id}')" class="delete-btn">Delete</button>`;
                        }

                        actionButtons += '</div>';
                        const actionButton = actionButtons;

                        html += `
                            <tr>
                                <td>${token.token_id}</td>
                                <td class="card-info">${token.payment_token}</td>
                                <td>${cardInfo}</td>
                                <td class="${statusClass}">${status}</td>
                                <td class="card-info">${createdDate}</td>
                                <td class="token-reference">${tokenReference}</td>
                                <td>${actionButton}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching tokens:', error);
                    container.innerHTML = `<div class="error-message">${escapeHtml(error.message)}</div>`;
                });
        }

        function getStatusText(status) {
            switch(status) {
                case 0: return 'Pending';
                case 1: return 'Failed';
                case 2: return 'Ready';
                case 3: return 'Deleted';
                case 4: return 'Pending Delete';
                default: return 'Unknown';
            }
        }

        function getStatusClass(status) {
            switch(status) {
                case 0: return 'status-pending';
                case 1: return 'status-failed';
                case 2: return 'status-ready';
                case 3: return 'status-deleted';
                case 4: return 'status-deleted';
                default: return '';
            }
        }

        function getPaymentMethodText(method) {
            switch(method) {
                case 0: return 'Visa';
                case 1: return 'Mastercard';
                case 2: return 'Amex';
                case 3: return 'Bancontact';
                case 4: return 'China UnionPay';
                case 5: return 'Maestro';
                case 6: return 'Diners';
                case 7: return 'Discover';
                case 8: return 'JCB';
                case 9: return 'Alipay';
                case 10: return 'WeChat';
                default: return 'Unknown';
            }
        }

        function useTokenForPayment(paymentToken) {
            // Redirect to token payment page with the selected token
            window.location.href = `token-payment-tokens.php?payment_token=${encodeURIComponent(paymentToken)}`;
        }

        function deleteToken(tokenId) {
            if (!confirm('Are you sure you want to delete this token? This action cannot be undone.')) {
                return;
            }

            // Disable the delete button to prevent multiple clicks
            const deleteButtons = document.querySelectorAll(`button[onclick="deleteToken('${tokenId}')"]`);
            deleteButtons.forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Deleting...';
            });

            fetch('api/delete-card-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token_id: tokenId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to delete token');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to delete token');
                    }

                    // Success - refresh the tokens list
                    alert('Token deleted successfully');
                    fetchTokens();
                })
                .catch(error => {
                    console.error('Error deleting token:', error);
                    alert('Error deleting token: ' + error.message);

                    // Re-enable the button on error
                    deleteButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.textContent = 'Delete';
                    });
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function onPayerReferenceChange() {
            // Clear current tokens when payer reference changes
            if (currentPayerReference !== document.getElementById('payer-reference').value.trim()) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Click "Load Tokens" or press Enter to view tokens for this payer</div>';
                currentPayerReference = '';
            }
        }

        function handleKeyPress(event) {
            // Check if Enter key was pressed
            if (event.key === 'Enter' || event.keyCode === 13) {
                event.preventDefault(); // Prevent form submission if inside a form
                fetchTokens();
            }
        }

        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            const code = codeElement.textContent || codeElement.innerText;

            navigator.clipboard.writeText(code).then(function() {
                // Visual feedback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = code;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Visual feedback for fallback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            });
        }

        function showTab(language, section) {
            // Hide all tab contents for this section
            const tabContents = document.querySelectorAll(`#${section}-section .tab-content`);
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons in this section
            const tabButtons = document.querySelectorAll(`#${section}-section .tab-button`);
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            const selectedContent = document.getElementById(`${section}-${language}-content`);
            if (selectedContent) {
                selectedContent.classList.add('active');
            }

            // Add active class to clicked button
            const selectedButton = document.querySelector(`#${section}-section [onclick="showTab('${language}', '${section}')"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }

        // Initialize page
        window.onload = function() {
            // Show PHP tabs by default for both sections
            showTab('php', 'get-tokens');
            showTab('php', 'delete-token');
        };
    </script>
</body>
</html>
