<?php
$config = require __DIR__ . '/config.php';

// Generate a random amount between 1 and 1000
$randomAmount = rand(1, 1000);

// Generate a random order ID with 8 random letters and numbers
function generateRandomOrderId($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomOrderId = '';
    for ($i = 0; $i < $length; $i++) {
        $randomOrderId .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomOrderId;
}

$randomOrderId = generateRandomOrderId();

// Check for the tid query parameter
$terminalId = isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - Checkout</title>
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

        /* Form Layout Improvements */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="number"],
        input[type="text"],
        input[type="url"],
        select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .readonly {
            background-color: #f9f9f9;
            color: #666;
        }

        .payment-section {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .checkbox-container input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            margin-bottom: 0;
        }

        .checkbox-container label {
            margin: 0;
            font-weight: normal;
        }

        select[multiple] {
            height: 150px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .help-text {
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
        }

        /* Submit Button Styling */
        .submit-btn {
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            max-width: 300px;
            margin: 20px auto;
            display: block;
        }

        .submit-btn:hover {
            background-color: #45a049;
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
            padding: 8px 12px;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
    <script>
        // Wait for DOM to load before adding event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Disable the QR code scanner checkbox if the payment method type is CARD
            const paymentMethodSelect = document.getElementById('payment_method_type');
            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', function() {
                    const qrScannerCheckbox = document.getElementById('activate_qr_code_scanner');
                    qrScannerCheckbox.disabled = this.value !== 'E_WALLET';
                    if (this.value !== 'E_WALLET') {
                        qrScannerCheckbox.checked = false;
                    }
                });
            }

            // Show PHP tab by default
            showTab('php');
        });

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

        function showTab(language) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            const selectedContent = document.getElementById(language + '-content');
            if (selectedContent) {
                selectedContent.classList.add('active');
            }

            // Add active class to clicked button
            const selectedButton = document.querySelector(`[onclick="showTab('${language}')"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/terminals.php">← Back to Terminals</a>
        </div>

        <h1>Send Payment to Terminal</h1>
        <h2>Terminal ID: <?php echo $terminalId; ?></h2>

        <form action="terminal_submit_payment.php?tid=<?php echo $terminalId; ?>" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="currency">Currency:</label>
                    <input type="text" id="currency" name="currency" value="<?php echo $config['currency']; ?>" class="readonly" readonly>
                </div>

                <div class="form-group">
                    <label for="order_id">Order ID:</label>
                    <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>
                </div>

                <div class="form-group">
                    <label for="terminal_id">Terminal ID:</label>
                    <input type="text" id="terminal_id" name="terminal_id" value="<?php echo $terminalId; ?>" class="readonly" readonly>
                </div>

                <div class="form-group full-width">
                    <div class="checkbox-container">
                        <input type="checkbox" id="show_tips" name="show_tips">
                        <label for="show_tips">Show tips</label>
                    </div>
                </div>
            </div>

            <div class="payment-section">
                <div class="section-title">Payment Method Control</div>

                <div class="form-group">
                    <label for="payment_method_type">Payment method type:</label>
                    <select id="payment_method_type" name="payment_method_type">
                        <option value="CARD">Card</option>
                        <option value="E_WALLET">E-Wallet</option>
                    </select>
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" id="activate_qr_code_scanner" name="activate_qr_code_scanner" disabled>
                    <label for="activate_qr_code_scanner">Activate QR code scanner</label>
                </div>
            </div>

            <div class="payment-section">
                <div class="section-title">Accepts Only</div>
                <div class="help-text">Press ⌘ / Ctrl and click to select multiple payment methods</div>

                <select id="accepts_only" name="accepts_only[]" multiple>
                    <option value="VISA">Visa</option>
                    <option value="MASTERCARD">Mastercard</option>
                    <option value="AMEX">Amex</option>
                    <option value="BAN_CONTACT">Ban Contact</option>
                    <option value="CHINA_UNION_PAY">China Union Pay</option>
                    <option value="MAESTRO">Maestro</option>
                    <option value="DINERS">Diners</option>
                    <option value="DISCOVER">Discover</option>
                    <option value="JCB">JCB</option>
                    <option value="ALIPAY">Alipay</option>
                    <option value="WECHAT">WeChat</option>
                </select>
            </div>

            <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

            <button type="submit" class="submit-btn">Pay</button>
        </form>

        <div class="links">
            <a href="/terminals.php">Back to terminal list</a> | <a href="/index.php">Main menu</a>
        </div>

        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error-message">Error: ' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>

        <div class="dev-info">
            <h2>Developer Information</h2>
            <p>This payment form accepts the following parameters:</p>
            <ul>
               <li><strong>Amount:</strong> The payment amount in minor units (e.g., 2000 = £20.00)</li>
               <li><strong>Currency:</strong> Fixed as GBP for this demo (it can be configured per store)</li>
               <li><strong>Order ID:</strong> Unique order identifier, auto-generated but can be modified</li>
               <li><strong>Terminal ID:</strong> Required terminal serial number for payment processing</li>
               <li><strong>Show Tips:</strong> When enabled, displays tipping options on terminal</li>
               <li><strong>Payment Method Control:</strong>
                   <ul>
                       <li>Enable specific payment flows (Card or E-Wallet)</li>
                       <li>QR scanner activation for E-Wallet payments (disabled if Card is selected)</li>
                   </ul>
               </li>
               <li><strong>Accepts Only:</strong> Multi-select payment method filter
                   <ul>
                       <li>Card: Visa, Mastercard, Amex, Ban Contact, China Union Pay, Maestro, Diners, Discover, JCB</li>
                       <li>E-Wallet: Alipay, WeChat</li>
                       <li>If none selected, all methods are accepted</li>
                   </ul>
               </li>
            </ul>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Initiate Payment</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyPayTerminalService</code></p>
                <p><strong>Method:</strong> <code>Pay()</code></p>
                <p><strong>Request:</strong> <code>PayRequest</code></p>
                <p><strong>Response:</strong> <code>stream PayResponse</code></p>
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

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PayRequest;
use Grpc\ChannelCredentials;

// Configuration
$HOSTNAME = "grpc-staging.kodypay.com";
$API_KEY = "your-api-key";

// Step 1: Initialize SDK client with SSL credentials
$client = new KodyPayTerminalServiceClient($HOSTNAME, [
    'credentials' => ChannelCredentials::createSsl()
]);

// Step 2: Set authentication headers with your API key
$metadata = ['X-API-Key' => [$API_KEY]];

// Step 3: Create PayRequest and configure payment parameters
$request = new PayRequest();
$request->setStoreId('your-store-id');
$request->setAmount("10.00");                    // Payment amount
$request->setTerminalId('your-terminal-id');     // Target terminal
$request->setShowTips(true);                     // Enable tip options
// Optional: Set payment method, order ID, etc.
// $request->setOrderId('ORDER123');
// $request->setPaymentReference('REF123');

// Step 4: Call Pay() method and process streaming response
$call = $client->Pay($request, $metadata);
foreach ($call->responses() as $response) {
    echo "Payment ID: " . $response->getPaymentId() . PHP_EOL;
    echo "Status: " . $response->getStatus() . PHP_EOL;

    // Handle payment data when available
    if ($response->getPaymentData()) {
        echo "Total Amount: " . $response->getPaymentData()->getTotalAmount() . PHP_EOL;
        echo "Sale Amount: " . $response->getPaymentData()->getSaleAmount() . PHP_EOL;
    }

    // Exit after first response or continue for status updates
    break;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.pay.v1.KodyPayTerminalServiceGrpc;
import com.kodypay.grpc.pay.v1.PayRequest;
import com.kodypay.grpc.pay.v1.PayResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

import java.math.BigDecimal;
import java.util.concurrent.TimeUnit;

public class InitiateTerminalPaymentExample {
    public static final String HOSTNAME = "grpc-staging.kodypay.com";
    public static final String API_KEY = "your-api-key";

    public static void main(String[] args) {
        // Payment parameters
        String storeId = "your-store-id";
        String terminalId = "your-terminal-id";
        BigDecimal amount = new BigDecimal("10.00");

        initiatePayment(storeId, terminalId, amount);
    }

    private static void initiatePayment(String storeId, String terminalId, BigDecimal amount) {
        // Step 1: Create metadata with API key
        Metadata metadata = new Metadata();
        metadata.put(Metadata.Key.of("X-API-Key", Metadata.ASCII_STRING_MARSHALLER), API_KEY);

        // Step 2: Build secure channel and create client
        var client = KodyPayTerminalServiceGrpc.newBlockingStub(
            ManagedChannelBuilder.forAddress(HOSTNAME, 443)
                .useTransportSecurity()
                .idleTimeout(3, TimeUnit.MINUTES)
                .keepAliveTimeout(3, TimeUnit.MINUTES)
                .build())
            .withInterceptors(MetadataUtils.newAttachHeadersInterceptor(metadata));

        // Step 3: Create PayRequest and configure payment parameters
        PayRequest request = PayRequest.newBuilder()
            .setStoreId(storeId)
            .setAmount(amount.toString())
            .setTerminalId(terminalId)
            .setShowTips(true)
            // Optional: Add order ID, payment reference, etc.
            // .setOrderId("ORDER123")
            // .setPaymentReference("REF123")
            .build();

        // Step 4: Call Pay() method and process streaming response
        PayResponse response = client.pay(request).next();

        System.out.println("Payment ID: " + response.getPaymentId());
        System.out.println("Status: " + response.getStatus());

        // Handle payment data when available
        if (response.hasPaymentData()) {
            System.out.println("Total Amount: " + response.getPaymentData().getTotalAmount());
            System.out.println("Sale Amount: " + response.getPaymentData().getSaleAmount());
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
import kody_clientsdk_python.pay.v1.pay_pb2 as kody_model
import kody_clientsdk_python.pay.v1.pay_pb2_grpc as kody_client

def initiate_terminal_payment():
    # Configuration
    hostname = "grpc-staging.kodypay.com:443"
    api_key = "your-api-key"

    # Payment parameters
    store_id = "your-store-id"
    terminal_id = "your-terminal-id"
    amount = "10.00"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(hostname, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyPayTerminalServiceStub(channel)
    metadata = [("x-api-key", api_key)]

    # Step 3: Create PayRequest and configure payment parameters
    request = kody_model.PayRequest(
        store_id=store_id,
        amount=amount,
        terminal_id=terminal_id,
        show_tips=True
        # Optional: Add order ID, payment reference, etc.
        # order_id="ORDER123",
        # payment_reference="REF123"
    )

    # Step 4: Call Pay() method and process streaming response
    response_iterator = client.Pay(request, metadata=metadata)

    # Process the first response from the stream
    for response in response_iterator:
        print(f"Payment ID: {response.payment_id}")
        print(f"Status: {kody_model.PaymentStatus.Name(response.status)}")

        # Handle payment data when available
        if response.HasField("payment_data"):
            print(f"Total Amount: {response.payment_data.total_amount}")
            print(f"Sale Amount: {response.payment_data.sale_amount}")

        # Exit after first response or continue for status updates
        break

if __name__ == "__main__":
    initiate_terminal_payment()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('dotnet-code')">Copy</button>
                        <pre id="dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Pay.V1;

class Program
{
    static async Task Main(string[] args)
    {
        // Configuration
        var HOSTNAME = "grpc-staging.kodypay.com";
        var API_KEY = "your-api-key";

        // Payment parameters
        var storeId = "your-store-id";
        var terminalId = "your-terminal-id";
        var amount = "10.00";

        // Step 1: Create secure channel
        var channel = GrpcChannel.ForAddress("https://" + HOSTNAME);

        // Step 2: Create client
        var client = new KodyPayTerminalService.KodyPayTerminalServiceClient(channel);

        // Step 3: Set authentication headers with API key
        var metadata = new Metadata { { "X-API-Key", API_KEY } };

        // Step 4: Create PayRequest and configure payment parameters
        var request = new PayRequest
        {
            StoreId = storeId,
            Amount = amount,
            TerminalId = terminalId,
            ShowTips = true
            // Optional: Add order ID, payment reference, etc.
            // OrderId = "ORDER123",
            // PaymentReference = "REF123"
        };

        // Step 5: Call Pay() method and process streaming response
        using var call = client.Pay(request, metadata);
        if (await call.ResponseStream.MoveNext())
        {
            var response = call.ResponseStream.Current;
            Console.WriteLine($"Payment ID: {response.PaymentId}");
            Console.WriteLine($"Status: {response.Status}");

            // Handle payment data when available
            if (response.PaymentData != null)
            {
                Console.WriteLine($"Total Amount: {response.PaymentData.TotalAmount}");
                Console.WriteLine($"Sale Amount: {response.PaymentData.SaleAmount}");
            }
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
