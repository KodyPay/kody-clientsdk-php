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

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        button:disabled {
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

        .redirect-button {
            background-color: #007bff;
            margin-top: 15px;
            padding: 12px 24px;
        }

        .redirect-button:hover {
            background-color: #0056b3;
        }

        .field-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: -15px;
            margin-bottom: 15px;
        }

        .regenerate-button {
            width: auto !important;
            margin-bottom: 20px;
            background-color: #6c757d;
            padding: 8px 16px;
            font-size: 14px;
        }

        .regenerate-button:hover {
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
            <p>This page demonstrates how token-based payments work. In a real application, payment tokens would be securely stored and associated with customer accounts.</p>

            <h3>Token Payment Flow</h3>
            <ul>
                <li><strong>Token Storage:</strong> Payment method tokens are securely stored after initial customer authorization</li>
                <li><strong>Token Selection:</strong> Customers can select from their saved payment methods</li>
                <li><strong>Payment Processing:</strong> The selected token is used to process the payment without requiring card details</li>
                <li><strong>Security:</strong> Tokens are encrypted and can be revoked if needed</li>
            </ul>

            <h3>CreateCardToken API Usage</h3>
            <p>Before customers can use saved payment methods, you need to create tokens using the <code>CreateCardToken</code> API. This application provides an endpoint at <code>/api/create-card-token.php</code> for this purpose.</p>

            <h4>API Endpoint</h4>
            <p><strong>POST</strong> <code>/api/create-card-token.php</code></p>

            <h4>Request Body (JSON)</h4>
            <pre><code>{
    "payer_reference": "customer_123",
    "return_url": "<?php echo htmlspecialchars($returnUrl); ?>",
    "idempotency_uuid": "<?php echo htmlspecialchars($randomIdempotencyUuid); ?>",
    "token_reference": "<?php echo htmlspecialchars($randomTokenReference); ?>",
    "payer_email_address": "customer@example.com",
    "payer_phone_number": "+1234567890",
    "recurring_processing_model": "SUBSCRIPTION",
    "payer_statement": "Statement text",
    "metadata": "{\"customer_name\": \"John Doe\", \"card_alias\": \"My Primary Card\"}"
}</code></pre>

            <h4>Response Format</h4>
            <pre><code>// Success Response
{
    "response": {
        "token_id": "P._pay.7bG7U1Y",
        "create_token_url": "https://p-staging.kody.com/P._pay.7bG7U1Y"
    }
}

// Error Response
{
    "success": false,
    "error_message": "Missing required field: payer_reference"
}</code></pre>

            <h3>Required Fields</h3>
            <ul>
                <li><strong>payer_reference:</strong> Customer ID or unique identifier</li>
                <li><strong>return_url:</strong> Callback URL (automatically set)</li>
                <li><strong>recurring_processing_model:</strong> Payment processing model</li>
            </ul>

            <h3>Optional Fields</h3>
            <ul>
                <li><strong>payer_email_address:</strong> Customer email (recommended for fraud checks)</li>
                <li><strong>payer_phone_number:</strong> Customer phone number</li>
                <li><strong>payer_statement:</strong> Text shown on bank statement (max 22 characters)</li>
                <li><strong>metadata:</strong> Additional data in JSON format</li>
                <li><strong>token_reference:</strong> Your reference for the token (auto-generated)</li>
                <li><strong>idempotency_uuid:</strong> Unique request identifier (auto-generated)</li>
            </ul>

            <h3>Token Creation Flow</h3>
            <ol>
                <li><strong>Create Token Request:</strong> Call <code>CreateCardToken</code> with customer details</li>
                <li><strong>Customer Authorization:</strong> Redirect customer to the returned URL</li>
                <li><strong>Token Storage:</strong> After authorization, token is stored securely</li>
                <li><strong>Future Payments:</strong> Use <code>PayWithCardToken</code> for subsequent payments</li>
            </ol>

            <h3>Implementation Notes</h3>
            <ul>
                <li><strong>Token Format:</strong> Each token has a unique identifier (e.g., 'P._pay.7bG7U1Y')</li>
                <li><strong>Token Types:</strong> Support for various payment methods (cards, digital wallets, etc.)</li>
                <li><strong>Security:</strong> Tokens are one-way encrypted and cannot be reverse-engineered</li>
                <li><strong>Compliance:</strong> Token storage follows PCI DSS compliance requirements</li>
                <li><strong>Return URL:</strong> Automatically set to <code><?php echo htmlspecialchars($returnUrl); ?></code></li>
            </ul>

            <h3>Response Handling</h3>
            <p>The API returns different response formats for success and error cases:</p>
            <ul>
                <li><strong>Success:</strong> Returns a <code>response</code> object containing the token details</li>
                <li><strong>Error:</strong> Returns <code>success: false</code> with an <code>error_message</code> field</li>
                <li><strong>Token URL:</strong> The <code>create_token_url</code> is where users complete card tokenization</li>
                <li><strong>Token ID:</strong> Unique identifier for tracking the tokenization process</li>
            </ul>

            <p>For more information about implementing token payments with Kody, please refer to the <a href="https://api-docs.kody.com" target="_blank">Kody API Documentation</a>.</p>
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
