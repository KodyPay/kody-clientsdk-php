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

// Sanitize error message if present
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
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

        label {
            display: block;
            margin-top: 10px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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

        .expiration-fields {
            display: none;
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fafafa;
        }

        form button,
        button[type="submit"] {
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
        button[type="submit"]:hover {
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

        .dev-info .nested-list {
            list-style-type: disc;
            margin-left: 20px;
        }

        .display-hide {
            display: none;
        }

        #payment-frame {
            width: 80%;
            max-width: 768px;
            height: 640px;
            border: 0;
            border-radius: 4px;
            margin: 24px auto;
            display: none;
        }

        #payment-status {
            display: none;
            margin: 24px auto;
            text-align: center;
        }

        .payment-status-expired {
            color: blue;
        }
        .payment-status-success {
            color: green;
        }
        .payment-status-error {
            color: red;
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

        <h1>Checkout</h1>

        <?php if ($errorMessage): ?>
            <div class="error-message">Error: <?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form id="checkout-form" action="checkout_payment.php" method="POST">
            <label for="amount">Amount (minor units):</label>
            <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" required min="1" step="1">

            <label for="currency">Currency:</label>
            <select id="currency" name="currency" required>
                <?php foreach ($config['currencies'] as $currencyOption): ?>
                    <option value="<?php echo htmlspecialchars($currencyOption); ?>"
                        <?php echo ($config['currency'] === $currencyOption) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($currencyOption); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="order_id">Order ID:</label>
            <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>

            <div class="checkbox-container display-hide">
                <input type="checkbox" id="enable_iframe" name="enable_iframe" checked>
                <label for="enable_iframe">Enable iframe mode</label>
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="enable_expiration" name="enable_expiration" onchange="toggleExpirationFields()">
                <label for="enable_expiration">Enable expiration</label>
            </div>

            <div id="expiration_fields" class="expiration-fields">
                <label for="expiring_seconds">Expiring seconds:</label>
                <input type="number" id="expiring_seconds" name="expiring_seconds" min="0"
                    value="<?php echo htmlspecialchars($config['expiring_seconds'] ?? '1800'); ?>">

                <label for="show_timer">Show timer:</label>
                <select id="show_timer" name="show_timer">
                    <option value="true">true</option>
                    <option value="false">false</option>
                </select>
            </div>

            <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

            <button type="submit">Pay</button>
        </form>

        <iframe id="payment-frame"></iframe>

        <h3 id="payment-status" style="display: none;"></h3>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>

        <div class="dev-info">
            <h2>Developer Information</h2>
            <p>This page demonstrates how to initiate a payment using the KodyEcomPaymentsService API. The form above collects the necessary information and sends a payment request to the backend.</p>
            <ul>
                <li><strong>Amount:</strong> The amount to be charged in minor units (e.g., 2000 for $20.00). This corresponds to the <code>amount_minor_units</code> field in the API.</li>
                <li><strong>Currency:</strong> The ISO 4217 three-letter currency code (e.g., GBP, HKD or USD) in which the payment will be made.</li>
                <li><strong>Order ID:</strong> Your unique identifier for this order. This can be reused if the same order has multiple payments.</li>
                <li><strong>Store ID:</strong> Your Kody store identifier (hidden field). This is required for all API calls.</li>
                <li><strong>Enable expiration:</strong> Configure payment expiration settings.
                    <ul class="nested-list">
                        <li><strong>Expiring seconds:</strong> Timeout duration in seconds (default: 1800). After this period, the payment will expire.</li>
                        <li><strong>Show timer:</strong> When enabled, displays a countdown timer on the payment page to indicate remaining time.</li>
                    </ul>
                </li>
            </ul>

            <h3>API Response</h3>
            <p>Upon successful submission, the API will return:</p>
            <ul>
                <li><strong>Payment ID:</strong> A unique identifier created by Kody for this payment</li>
                <li><strong>Payment URL:</strong> The URL where the customer will be redirected to complete the payment</li>
            </ul>

            <p>After payment completion, the user will be redirected to the return URL specified in the backend configuration.</p>

            <h3>Test Cards</h3>
            <p>For testing purposes, you can use test cards available in the <a href="https://api-docs.kody.com/docs/getting-started/test-cards" target="_blank">Test Cards Documentation</a>.</p>

            <p>For more detailed information about the API, please refer to the <a href="https://api-docs.kody.com/docs/payments-api/ecom-payments/#1-initiate-payment" target="_blank">Kody Payments API Documentation</a>.</p>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Payment Initiation</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Method:</strong> <code>InitiatePayment()</code></p>
                <p><strong>Request:</strong> <code>PaymentInitiationRequest</code></p>
                <p><strong>Response:</strong> <code>PaymentInitiationResponse</code></p>
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
use Com\Kodypay\Grpc\Ecom\V1\PaymentInitiationRequest;
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

// Step 3: Create PaymentInitiationRequest and set required fields
$request = new PaymentInitiationRequest();
$request->setStoreId('your-store-id');
$request->setPaymentReference('unique-payment-ref-' . uniqid());
$request->setAmountMinorUnits(2000); // £20.00
$request->setCurrency('GBP');
$request->setOrderId('order-' . uniqid());
$request->setReturnUrl('https://your-domain.com/return');

// Step 4: Optional fields
$request->setPayerEmailAddress('customer@example.com');
$request->setPayerIpAddress($_SERVER['REMOTE_ADDR']);
$request->setPayerLocale('en_GB');

// Step 5: Call InitiatePayment() method and wait for response
list($response, $status) = $client->InitiatePayment($request, $metadata)->wait();

// Step 6: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 7: Process response
if ($response->hasResponse()) {
    $responseData = $response->getResponse();
    echo "Payment ID: " . $responseData->getPaymentId() . PHP_EOL;
    echo "Payment URL: " . $responseData->getPaymentUrl() . PHP_EOL;

    // Redirect user to payment URL
    header('Location: ' . $responseData->getPaymentUrl());
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
import com.kodypay.grpc.ecom.v1.PaymentInitiationRequest;
import com.kodypay.grpc.ecom.v1.PaymentInitiationResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class InitiatePaymentExample {
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

        // Step 3: Create PaymentInitiationRequest and set required fields
        PaymentInitiationRequest request = PaymentInitiationRequest.newBuilder()
            .setStoreId("your-store-id")
            .setPaymentReference("unique-payment-ref-" + System.currentTimeMillis())
            .setAmountMinorUnits(2000) // £20.00
            .setCurrency("GBP")
            .setOrderId("order-" + System.currentTimeMillis())
            .setReturnUrl("https://your-domain.com/return")
            .setPayerEmailAddress("customer@example.com")
            .setPayerLocale("en_GB")
            .build();

        // Step 4: Call InitiatePayment() method and get response
        PaymentInitiationResponse response = client.initiatePayment(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            var responseData = response.getResponse();
            System.out.println("Payment ID: " + responseData.getPaymentId());
            System.out.println("Payment URL: " + responseData.getPaymentUrl());

            // Redirect user to payment URL
            // response.sendRedirect(responseData.getPaymentUrl());
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
import time
import kody_clientsdk_python.ecom.v1.ecom_pb2 as kody_model
import kody_clientsdk_python.ecom.v1.ecom_pb2_grpc as kody_client

def initiate_payment():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create PaymentInitiationRequest and set required fields
    request = kody_model.PaymentInitiationRequest(
        store_id="your-store-id",
        payment_reference=f"unique-payment-ref-{int(time.time())}",
        amount_minor_units=2000,  # £20.00
        currency="GBP",
        order_id=f"order-{int(time.time())}",
        return_url="https://your-domain.com/return",
        payer_email_address="customer@example.com",
        payer_locale="en_GB"
    )

    # Step 4: Call InitiatePayment() method and get response
    response = client.InitiatePayment(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        response_data = response.response
        print(f"Payment ID: {response_data.payment_id}")
        print(f"Payment URL: {response_data.payment_url}")

        # Redirect user to payment URL
        # webbrowser.open(response_data.payment_url)
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    initiate_payment()</code></pre>
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

        // Step 4: Create PaymentInitiationRequest and set required fields
        var request = new PaymentInitiationRequest
        {
            StoreId = "your-store-id",
            PaymentReference = $"unique-payment-ref-{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}",
            AmountMinorUnits = 2000, // £20.00
            Currency = "GBP",
            OrderId = $"order-{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}",
            ReturnUrl = "https://your-domain.com/return",
            PayerEmailAddress = "customer@example.com",
            PayerLocale = "en_GB"
        };

        // Step 5: Call InitiatePayment() method and get response
        var response = await client.InitiatePaymentAsync(request, metadata);

        // Step 6: Process response
        if (response.ResponseCase == PaymentInitiationResponse.ResponseOneofCase.Response)
        {
            var responseData = response.Response;
            Console.WriteLine($"Payment ID: {responseData.PaymentId}");
            Console.WriteLine($"Payment URL: {responseData.PaymentUrl}");

            // Redirect user to payment URL
            // Response.Redirect(responseData.PaymentUrl);
        }
        else if (response.ResponseCase == PaymentInitiationResponse.ResponseOneofCase.Error)
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
        function toggleExpirationFields() {
            const checkbox = document.getElementById('enable_expiration');
            const fields = document.getElementById('expiration_fields');

            if (checkbox && fields) {
                fields.style.display = checkbox.checked ? 'block' : 'none';
            }
        }

        async function handleSubmit() {
            document.getElementById('checkout-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const paymentFrame = document.getElementById('payment-frame');
                const paymentStatus = document.getElementById("payment-status");

                paymentFrame.style.display = 'none'
                paymentStatus.style.display = 'none'

                fetch(this.action, {
                    method: this.method,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.paymentUrl) {
                        paymentFrame.src = data.paymentUrl + (data.paymentUrl.includes('?') ? '&' : '?') + 'isEmbeddedInIframe=1';
                        paymentFrame.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
            });
        }

        async function handleMessage() {
            window.addEventListener("message", (event) => {
            // Check if message is payment complete
            if (event.data && event.data.type === "PAYMENT_COMPLETE") {
                // Find and hide the iframe
                const paymentFrame = document.getElementById('payment-frame');
                paymentFrame.style.display = "none";

                const paymentStatus = document.getElementById("payment-status");
                paymentStatus.textContent = `Payment ${event.data.outcome}`;
                paymentStatus.classList.add(`payment-status-${event.data.outcome}`);
                paymentStatus.style.display = 'block';
            }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleExpirationFields();
            handleSubmit();
            handleMessage();
        });
    </script>
</body>
</html>
