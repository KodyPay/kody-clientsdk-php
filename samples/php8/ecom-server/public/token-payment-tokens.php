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


        .card-info {
            font-size: 12px;
            color: #666;
        }

        .token-reference {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }

        /* Payment Form Styles */
        .payment-form-section {
            margin: 40px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            display: none;
        }

        .payment-form-section h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .form-actions {
            margin-top: 20px;
        }

        .pay-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .pay-button:hover {
            background-color: #218838;
        }

        .pay-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .cancel-button {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-button:hover {
            background-color: #5a6268;
        }

        .payment-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            display: none;
        }

        .payment-result.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .payment-result.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
    <link rel="stylesheet" href="css/sdk-common.php">
    <script src="js/bubble.php"></script>
    <script src="js/sdk-common.php"></script>
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
            // Show the payment form section
            document.getElementById('payment-form-section').style.display = 'block';

            // Set the payment token in the form
            document.getElementById('payment-token').value = paymentToken;

            // Generate a unique payment reference
            document.getElementById('payment-reference').value = 'payment_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

            // Scroll to the payment form
            document.getElementById('payment-form-section').scrollIntoView({ behavior: 'smooth' });
        }

        function hidePaymentForm() {
            document.getElementById('payment-form-section').style.display = 'none';
            document.getElementById('payment-result').style.display = 'none';
            document.getElementById('payment-form').reset();
        }

        function handlePaymentSubmit(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const payButton = document.getElementById('pay-button');
            const resultDiv = document.getElementById('payment-result');

            // Disable the pay button and show loading
            payButton.disabled = true;
            payButton.textContent = 'Processing...';
            resultDiv.style.display = 'none';

            // Convert FormData to JSON
            const paymentData = {};
            formData.forEach((value, key) => {
                paymentData[key] = value;
            });

            // Convert amount to integer
            paymentData.amount_minor_units = parseInt(paymentData.amount_minor_units);

            fetch('api/pay-with-card-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(error => {
                        throw new Error(error.error || 'Payment failed');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Payment successful
                    resultDiv.className = 'payment-result success';
                    resultDiv.innerHTML = `
                        <h3>✅ Payment Successful!</h3>
                        <p><strong>Payment ID:</strong> ${data.payment_id}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                        ${data.sale_data ? `
                            <p><strong>Amount:</strong> ${(data.sale_data.amount_minor_units / 100).toFixed(2)} ${data.sale_data.currency}</p>
                            <p><strong>Payment Reference:</strong> ${data.sale_data.payment_reference}</p>
                            ${data.sale_data.order_id ? `<p><strong>Order ID:</strong> ${data.sale_data.order_id}</p>` : ''}
                        ` : ''}
                        ${data.payment_data ? `
                            <p><strong>PSP Reference:</strong> ${data.payment_data.psp_reference}</p>
                            <p><strong>Payment Method:</strong> ${data.payment_data.payment_method}</p>
                            <p><strong>Auth Status:</strong> ${data.payment_data.auth_status}</p>
                            ${data.payment_data.card ? `<p><strong>Card:</strong> **** ${data.payment_data.card.card_last_4_digits}</p>` : ''}
                        ` : ''}
                        <p><strong>Date:</strong> ${data.date_created || 'N/A'}</p>
                    `;
                } else {
                    throw new Error(data.error || 'Payment failed');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                resultDiv.className = 'payment-result error';
                resultDiv.innerHTML = `
                    <h3>❌ Payment Failed</h3>
                    <p>${escapeHtml(error.message)}</p>
                `;
            })
            .finally(() => {
                // Re-enable the pay button
                payButton.disabled = false;
                payButton.textContent = 'Process Payment';
                resultDiv.style.display = 'block';
            });
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
                    // Show more user-friendly error message
                    let errorMessage = error.message;
                    if (error.message.includes('Internal service error')) {
                        errorMessage = 'Delete failed due to a temporary service issue. Please try again in a few moments.';
                    } else if (error.message.includes('Connection failed')) {
                        errorMessage = 'Connection failed. Please check your network and try again.';
                    } else if (error.message.includes('Token not found')) {
                        errorMessage = 'Token not found. It may have already been deleted.';
                        // If token not found, refresh the list anyway
                        setTimeout(() => fetchTokens(), 1000);
                    }

                    alert('Error deleting token: ' + errorMessage);

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

        <!-- Payment Form Section -->
        <div id="payment-form-section" class="payment-form-section">
            <h2>💳 Pay with Selected Token</h2>
            <form id="payment-form" onsubmit="handlePaymentSubmit(event)">
                <div class="form-group">
                    <label for="payment-token">Payment Token:</label>
                    <input type="text" id="payment-token" name="payment_token" readonly>
                </div>

                <div class="form-group">
                    <label for="amount">Amount (minor units):</label>
                    <input type="number" id="amount" name="amount_minor_units" min="1" step="1" required>
                    <small>Enter amount in minor units (e.g., 2000 for £20.00)</small>
                </div>

                <div class="form-group">
                    <label for="currency">Currency:</label>
                    <select id="currency" name="currency" required>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="HKD">HKD - Hong Kong Dollar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment-reference">Payment Reference:</label>
                    <input type="text" id="payment-reference" name="payment_reference" required>
                    <small>Your unique reference for this payment</small>
                </div>

                <div class="form-group">
                    <label for="order-id">Order ID (optional):</label>
                    <input type="text" id="order-id" name="order_id">
                    <small>Your identifier for the order</small>
                </div>

                <div class="form-group">
                    <label for="payer-statement">Payer Statement (optional):</label>
                    <input type="text" id="payer-statement" name="payer_statement" maxlength="22">
                    <small>Text to appear on payer's bank statement (max 22 characters)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="pay-button" id="pay-button">Process Payment</button>
                    <button type="button" class="cancel-button" onclick="hidePaymentForm()">Cancel</button>
                </div>
            </form>

            <div id="payment-result" class="payment-result"></div>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Token Management</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Methods:</strong> <code>GetCardTokens()</code>, <code>DeleteCardToken()</code>, <code>PayWithCardToken()</code></p>
                <p><strong>Requests:</strong> <code>GetCardTokensRequest</code>, <code>DeleteCardTokenRequest</code>, <code>PayWithCardTokenRequest</code></p>
                <p><strong>Responses:</strong> <code>GetCardTokensResponse</code>, <code>DeleteCardTokenResponse</code>, <code>PaymentDetailsResponse</code></p>
            </div>

            <!-- Section Navigation -->
            <div class="section-nav">
                <button onclick="scrollToSection('get-tokens-section')" class="nav-btn">📋 Get Tokens</button>
                <button onclick="scrollToSection('delete-token-section')" class="nav-btn">🗑️ Delete Token</button>
                <button onclick="scrollToSection('pay-with-card-token-section')" class="nav-btn">💳 Pay with Token</button>
            </div>

            <div class="collapsible-section">
                <div class="section-header" onclick="toggleSection('get-tokens-section')">
                    <h3>📋 Get Card Tokens - SDK Examples</h3>
                    <span class="toggle-icon">−</span>
                </div>
                <div class="code-section" id="get-tokens-section">
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
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
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
                <div id="python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('python-code')">Copy</button>
                        <pre id="python-code"><code>import grpc
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
                <div id="dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('dotnet-code')">Copy</button>
                        <pre id="dotnet-code"><code>using Grpc.Core;
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
            </div>

            <div class="collapsible-section">
                <div class="section-header" onclick="toggleSection('delete-token-section')">
                    <h3>🗑️ Delete Card Token - SDK Examples</h3>
                    <span class="toggle-icon">−</span>
                </div>
                <div class="code-section" id="delete-token-section">
                <div class="tabs">
                    <button class="tab-button" onclick="showDeleteTab('php')">PHP</button>
                    <button class="tab-button" onclick="showDeleteTab('java')">Java</button>
                    <button class="tab-button" onclick="showDeleteTab('python')">Python</button>
                    <button class="tab-button" onclick="showDeleteTab('dotnet')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="delete-php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-php-code')">Copy</button>
                        <pre id="delete-php-code"><code>&lt;?php
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
$request->setTokenId('token-id-to-delete'); // or use setTokenReference('your-token-reference')

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
                <div id="delete-java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-java-code')">Copy</button>
                        <pre id="delete-java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
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
            .setTokenId("token-id-to-delete") // or use setTokenReference("your-token-reference")
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
                <div id="delete-python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-python-code')">Copy</button>
                        <pre id="delete-python-code"><code>import grpc
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
        token_id="token-id-to-delete"  # or use token_reference="your-token-reference"
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
                <div id="delete-dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('delete-dotnet-code')">Copy</button>
                        <pre id="delete-dotnet-code"><code>using Grpc.Core;
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
            TokenId = "token-id-to-delete" // or use TokenReference = "your-token-reference"
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

            <div class="collapsible-section">
                <div class="section-header" onclick="toggleSection('pay-with-card-token-section')">
                    <h3>💳 Pay With Card Token - SDK Examples</h3>
                    <span class="toggle-icon">−</span>
                </div>
                <div class="code-section" id="pay-with-card-token-section">
                <div class="tabs">
                    <button class="tab-button" onclick="showPayWithCardTokenTab('php')">PHP</button>
                    <button class="tab-button" onclick="showPayWithCardTokenTab('java')">Java</button>
                    <button class="tab-button" onclick="showPayWithCardTokenTab('python')">Python</button>
                    <button class="tab-button" onclick="showPayWithCardTokenTab('dotnet')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="pay-php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('pay-php-code')">Copy</button>
                        <pre id="pay-php-code"><code>&lt;?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\PayWithCardTokenRequest;
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

// Step 3: Create PayWithCardTokenRequest and set required fields
$request = new PayWithCardTokenRequest();
$request->setStoreId('your-store-id');
$request->setIdempotencyUuid(uniqid('', true)); // Generate unique idempotency key
$request->setPaymentToken('payment-token-from-get-tokens-response');
$request->setAmountMinorUnits(2000); // £20.00 in minor units
$request->setCurrency('GBP');
$request->setPaymentReference('payment_' . time());

// Optional fields
$request->setOrderId('order-123');
$request->setPayerStatement('My Store Purchase');

// Step 4: Call PayWithCardToken() method and wait for response
list($response, $status) = $client->PayWithCardToken($request, $metadata)->wait();

// Step 5: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 6: Process payment response
if ($response->hasResponse()) {
    $paymentDetails = $response->getResponse();

    echo "Payment ID: " . $paymentDetails->getPaymentId() . PHP_EOL;
    echo "Status: " . $paymentDetails->getStatus() . PHP_EOL;

    if ($paymentDetails->hasSaleData()) {
        $saleData = $paymentDetails->getSaleData();
        echo "Amount: " . ($saleData->getAmountMinorUnits() / 100) . " " . $saleData->getCurrency() . PHP_EOL;
    }

    if ($paymentDetails->hasPaymentData()) {
        $paymentData = $paymentDetails->getPaymentData();
        echo "PSP Reference: " . $paymentData->getPspReference() . PHP_EOL;
        echo "Auth Status: " . $paymentData->getAuthStatus() . PHP_EOL;
    }
} else if ($response->hasError()) {
    $error = $response->getError();
    echo "Payment failed: " . $error->getMessage() . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="pay-java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('pay-java-code')">Copy</button>
                        <pre id="pay-java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
import com.kodypay.grpc.ecom.v1.PayWithCardTokenRequest;
import com.kodypay.grpc.ecom.v1.PaymentDetailsResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;
import java.util.UUID;

public class PayWithCardTokenExample {
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

        // Step 3: Create PayWithCardTokenRequest and set required fields
        PayWithCardTokenRequest request = PayWithCardTokenRequest.newBuilder()
            .setStoreId("your-store-id")
            .setIdempotencyUuid(UUID.randomUUID().toString())
            .setPaymentToken("payment-token-from-get-tokens-response")
            .setAmountMinorUnits(2000) // £20.00 in minor units
            .setCurrency("GBP")
            .setPaymentReference("payment_" + System.currentTimeMillis())
            .setOrderId("order-123") // Optional
            .setPayerStatement("My Store Purchase") // Optional
            .build();

        // Step 4: Call PayWithCardToken() method and get response
        PaymentDetailsResponse response = client.payWithCardToken(request);

        // Step 5: Process payment response
        if (response.hasResponse()) {
            var paymentDetails = response.getResponse();

            System.out.println("Payment ID: " + paymentDetails.getPaymentId());
            System.out.println("Status: " + paymentDetails.getStatus());

            if (paymentDetails.hasSaleData()) {
                var saleData = paymentDetails.getSaleData();
                System.out.println("Amount: " + (saleData.getAmountMinorUnits() / 100.0) + " " + saleData.getCurrency());
            }

            if (paymentDetails.hasPaymentData()) {
                var paymentData = paymentDetails.getPaymentData();
                System.out.println("PSP Reference: " + paymentData.getPspReference());
                System.out.println("Auth Status: " + paymentData.getAuthStatus());
            }
        } else if (response.hasError()) {
            var error = response.getError();
            System.out.println("Payment failed: " + error.getMessage());
        }
    }
}</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div id="pay-python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('pay-python-code')">Copy</button>
                        <pre id="pay-python-code"><code>import grpc
import uuid
import time
import kody_clientsdk_python.ecom.v1.ecom_pb2 as kody_model
import kody_clientsdk_python.ecom.v1.ecom_pb2_grpc as kody_client

def pay_with_card_token():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create PayWithCardTokenRequest and set required fields
    request = kody_model.PayWithCardTokenRequest(
        store_id="your-store-id",
        idempotency_uuid=str(uuid.uuid4()),
        payment_token="payment-token-from-get-tokens-response",
        amount_minor_units=2000,  # £20.00 in minor units
        currency="GBP",
        payment_reference=f"payment_{int(time.time())}",
        order_id="order-123",  # Optional
        payer_statement="My Store Purchase"  # Optional
    )

    # Step 4: Call PayWithCardToken() method and get response
    response = client.PayWithCardToken(request, metadata=metadata)

    # Step 5: Process payment response
    if response.HasField("response"):
        payment_details = response.response

        print(f"Payment ID: {payment_details.payment_id}")
        print(f"Status: {payment_details.status}")

        if payment_details.HasField("sale_data"):
            sale_data = payment_details.sale_data
            print(f"Amount: {sale_data.amount_minor_units / 100} {sale_data.currency}")

        if payment_details.HasField("payment_data"):
            payment_data = payment_details.payment_data
            print(f"PSP Reference: {payment_data.psp_reference}")
            print(f"Auth Status: {payment_data.auth_status}")

    elif response.HasField("error"):
        error = response.error
        print(f"Payment failed: {error.message}")

if __name__ == "__main__":
    pay_with_card_token()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="pay-dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('pay-dotnet-code')">Copy</button>
                        <pre id="pay-dotnet-code"><code>using Grpc.Core;
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

        // Step 4: Create PayWithCardTokenRequest and set required fields
        var request = new PayWithCardTokenRequest
        {
            StoreId = "your-store-id",
            IdempotencyUuid = Guid.NewGuid().ToString(),
            PaymentToken = "payment-token-from-get-tokens-response",
            AmountMinorUnits = 2000, // £20.00 in minor units
            Currency = "GBP",
            PaymentReference = $"payment_{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}",
            OrderId = "order-123", // Optional
            PayerStatement = "My Store Purchase" // Optional
        };

        // Step 5: Call PayWithCardToken() method and get response
        var response = await client.PayWithCardTokenAsync(request, metadata);

        // Step 6: Process payment response
        if (response.ResponseCase == PaymentDetailsResponse.ResponseOneofCase.Response)
        {
            var paymentDetails = response.Response;

            Console.WriteLine($"Payment ID: {paymentDetails.PaymentId}");
            Console.WriteLine($"Status: {paymentDetails.Status}");

            if (paymentDetails.SaleData != null)
            {
                var saleData = paymentDetails.SaleData;
                Console.WriteLine($"Amount: {saleData.AmountMinorUnits / 100.0} {saleData.Currency}");
            }

            if (paymentDetails.PaymentData != null)
            {
                var paymentData = paymentDetails.PaymentData;
                Console.WriteLine($"PSP Reference: {paymentData.PspReference}");
                Console.WriteLine($"Auth Status: {paymentData.AuthStatus}");
            }
        }
        else if (response.ResponseCase == PaymentDetailsResponse.ResponseOneofCase.Error)
        {
            var error = response.Error;
            Console.WriteLine($"Payment failed: {error.Message}");
        }
    }
}</code></pre>
                    </div>
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
            // Show the payment form section
            document.getElementById('payment-form-section').style.display = 'block';

            // Set the payment token in the form
            document.getElementById('payment-token').value = paymentToken;

            // Generate a unique payment reference
            document.getElementById('payment-reference').value = 'payment_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

            // Scroll to the payment form
            document.getElementById('payment-form-section').scrollIntoView({ behavior: 'smooth' });
        }

        function hidePaymentForm() {
            document.getElementById('payment-form-section').style.display = 'none';
            document.getElementById('payment-result').style.display = 'none';
            document.getElementById('payment-form').reset();
        }

        function handlePaymentSubmit(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const payButton = document.getElementById('pay-button');
            const resultDiv = document.getElementById('payment-result');

            // Disable the pay button and show loading
            payButton.disabled = true;
            payButton.textContent = 'Processing...';
            resultDiv.style.display = 'none';

            // Convert FormData to JSON
            const paymentData = {};
            formData.forEach((value, key) => {
                paymentData[key] = value;
            });

            // Convert amount to integer
            paymentData.amount_minor_units = parseInt(paymentData.amount_minor_units);

            fetch('api/pay-with-card-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(error => {
                        throw new Error(error.error || 'Payment failed');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Payment successful
                    resultDiv.className = 'payment-result success';
                    resultDiv.innerHTML = `
                        <h3>✅ Payment Successful!</h3>
                        <p><strong>Payment ID:</strong> ${data.payment_id}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                        ${data.sale_data ? `
                            <p><strong>Amount:</strong> ${(data.sale_data.amount_minor_units / 100).toFixed(2)} ${data.sale_data.currency}</p>
                            <p><strong>Payment Reference:</strong> ${data.sale_data.payment_reference}</p>
                            ${data.sale_data.order_id ? `<p><strong>Order ID:</strong> ${data.sale_data.order_id}</p>` : ''}
                        ` : ''}
                        ${data.payment_data ? `
                            <p><strong>PSP Reference:</strong> ${data.payment_data.psp_reference}</p>
                            <p><strong>Payment Method:</strong> ${data.payment_data.payment_method}</p>
                            <p><strong>Auth Status:</strong> ${data.payment_data.auth_status}</p>
                            ${data.payment_data.card ? `<p><strong>Card:</strong> **** ${data.payment_data.card.card_last_4_digits}</p>` : ''}
                        ` : ''}
                        <p><strong>Date:</strong> ${data.date_created || 'N/A'}</p>
                    `;
                } else {
                    throw new Error(data.error || 'Payment failed');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                resultDiv.className = 'payment-result error';
                resultDiv.innerHTML = `
                    <h3>❌ Payment Failed</h3>
                    <p>${escapeHtml(error.message)}</p>
                `;
            })
            .finally(() => {
                // Re-enable the pay button
                payButton.disabled = false;
                payButton.textContent = 'Process Payment';
                resultDiv.style.display = 'block';
            });
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
                    // Show more user-friendly error message
                    let errorMessage = error.message;
                    if (error.message.includes('Internal service error')) {
                        errorMessage = 'Delete failed due to a temporary service issue. Please try again in a few moments.';
                    } else if (error.message.includes('Connection failed')) {
                        errorMessage = 'Connection failed. Please check your network and try again.';
                    } else if (error.message.includes('Token not found')) {
                        errorMessage = 'Token not found. It may have already been deleted.';
                        // If token not found, refresh the list anyway
                        setTimeout(() => fetchTokens(), 1000);
                    }

                    alert('Error deleting token: ' + errorMessage);

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

        function showSectionTab(sectionId, language, contentPrefix = '') {
            // Hide all tab contents within the specified section
            const tabContents = document.querySelectorAll(`#${sectionId} .tab-content`);
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tab buttons within the section
            const tabButtons = document.querySelectorAll(`#${sectionId} .tab-button`);
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            const contentId = contentPrefix ? `${contentPrefix}-${language}-content` : `${language}-content`;
            const selectedContent = document.getElementById(contentId);
            if (selectedContent) {
                selectedContent.classList.add('active');
            }

            // Add active class to clicked button
            const functionName = getFunctionNameForSection(sectionId);
            const selectedButton = document.querySelector(`#${sectionId} [onclick="${functionName}('${language}')"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }

        function getFunctionNameForSection(sectionId) {
            switch(sectionId) {
                case 'delete-token-section': return 'showDeleteTab';
                case 'pay-with-card-token-section': return 'showPayWithCardTokenTab';
                default: return 'showTab';
            }
        }

        function showDeleteTab(language) {
            showSectionTab('delete-token-section', language, 'delete');
        }

        function showPayWithCardTokenTab(language) {
            showSectionTab('pay-with-card-token-section', language, 'pay');
        }

        // New UI Functions for Collapsible Sections
        function toggleSection(sectionId) {
            const targetSection = document.getElementById(sectionId).closest('.collapsible-section');
            const targetIcon = targetSection.querySelector('.toggle-icon');
            
            // Toggle the clicked section
            targetSection.classList.toggle('collapsed');
            
            if (targetSection.classList.contains('collapsed')) {
                targetIcon.textContent = '+';
            } else {
                targetIcon.textContent = '−';
            }
        }

        function expandSection(sectionId) {
            // Collapse all sections first
            const allSections = document.querySelectorAll('.collapsible-section');
            allSections.forEach(section => {
                const icon = section.querySelector('.toggle-icon');
                section.classList.add('collapsed');
                icon.textContent = '+';
            });
            
            // Expand the target section
            const targetSection = document.getElementById(sectionId).closest('.collapsible-section');
            const targetIcon = targetSection.querySelector('.toggle-icon');
            
            targetSection.classList.remove('collapsed');
            targetIcon.textContent = '−';
        }

        function scrollToSection(sectionId) {
            // Use expandSection for navigation buttons (accordion behavior)
            expandSection(sectionId);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Show PHP tab by default for all sections
            const sections = [
                { func: showTab, name: 'get-tokens' },
                { func: showDeleteTab, name: 'delete-token' },
                { func: showPayWithCardTokenTab, name: 'pay-with-card-token' }
            ];

            sections.forEach(section => section.func('php'));
            
            // Initialize accordion - collapse all sections except the first one
            const allSections = document.querySelectorAll('.collapsible-section');
            allSections.forEach((section, index) => {
                const icon = section.querySelector('.toggle-icon');
                if (index === 0) {
                    // Keep first section expanded
                    section.classList.remove('collapsed');
                    icon.textContent = '−';
                } else {
                    // Collapse other sections
                    section.classList.add('collapsed');
                    icon.textContent = '+';
                }
            });
        });
    </script>
</body>
</html>
