<?php
$config = require __DIR__ . '/config.php';
$functions = require_once __DIR__ . '/functions.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\RefundRequest;
use Grpc\ChannelCredentials;

header("Content-Type: text/html");

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['payment_id'])) {
            throw new Exception("Invalid access: Required payment details are missing.");
        }

        $paymentId = $_GET['payment_id'];
    }

    // Process refund form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid request: Security token mismatch.");
        }

        if (!isset($_POST['payment_id'], $_POST['refund_amount'])) {
            throw new Exception("Invalid input: Payment ID and refund amount are required.");
        }

        $paymentId = $_POST['payment_id'];
        $refundAmount = floatval($_POST['refund_amount']);

        if ($refundAmount <= 0) {
            throw new Exception("Invalid input: Refund amount must be greater than zero.");
        }

        // gRPC Refund Logic
        $client = new KodyEcomPaymentsServiceClient(
            $config['hostname'],
            ['credentials' => ChannelCredentials::createSsl()]
        );
        $metadata = ['X-API-Key' => [$config['api_key']]];

        $request = new RefundRequest();
        $request->setStoreId($config['store_id']);
        $request->setPaymentId($paymentId);
        $request->setAmount($refundAmount);

        $call = $client->Refund($request, $metadata);

        $refunds = [];
        foreach ($call->responses() as $response) {
            $refunds[] = [
                'status' => $response->getStatus(),
                'status_text' => $functions->getRefundStatusText($response->getStatus()),
                'payment_id' => $response->getPaymentId(),
                'payment_transaction_id' => $response->getpaymentTransactionId(),
                'date_created' => date('Y-m-d H:i:s', $response->getDateCreated()->getSeconds()),
                'total_paid_amount' => $response->getTotalPaidAmount(),
                'total_amount_requested' => $response->getTotalAmountRequested(),
                'total_amount_refunded' => $response->getTotalAmountRefunded(),
                'remaining_amount' => $response->getRemainingAmount(),
            ];
        }

        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<title>Refund Results</title>";
        echo "<style>
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

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                th, td {
                    padding: 12px;
                    text-align: left;
                    border: 1px solid #dee2e6;
                }

                th {
                    background-color: #e9ecef;
                    font-weight: bold;
                    color: #495057;
                }

                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                tr:hover {
                    background-color: #e8f4f8;
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
              </style>";
        echo "</head>";
        echo "<body>";
        echo "    <div class='container'>";
        echo "        <div class='top-nav'>";
        echo "            <a href='/transactions.php'>← Back to Transactions</a>";
        echo "        </div>";
        echo "        <h1>Refund Result</h1>";

        if (empty($refunds)) {
            echo "        <div class='error-message'>Refund Error: Exceeded the maximum refund amount.</div>";
        } else {
            foreach ($refunds as $refund) {
                echo "<table>";
                echo "<tbody>";
                echo "<tr>
                        <th>Status</th>
                        <td>" . htmlspecialchars($refund['status_text']) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Payment ID</th>
                        <td>" . htmlspecialchars($refund['payment_id']) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Payment Transaction ID</th>
                        <td>" . htmlspecialchars($refund['payment_transaction_id']) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Date Created</th>
                        <td>" . htmlspecialchars($refund['date_created']) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Total Paid Amount</th>
                        <td>" . htmlspecialchars(number_format($refund['total_paid_amount'], 2)) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Total Amount Requested</th>
                        <td>" . htmlspecialchars(number_format($refund['total_amount_requested'], 2)) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Total Amount Refunded</th>
                        <td>" . htmlspecialchars(number_format($refund['total_amount_refunded'], 2)) . "</td>
                      </tr>";
                echo "<tr>
                        <th>Remaining Amount</th>
                        <td>" . htmlspecialchars(number_format($refund['remaining_amount'], 2)) . "</td>
                      </tr>";
                echo "</tbody>";
                echo "        </table><br>";
            }
        }
        echo "        <div class='links'>";
        echo "            <a href='/refund-form.php?payment_id=" . htmlspecialchars($paymentId) . "'>Back to Refund Form</a> | <a href='/transactions.php'>Back to Transactions</a>";
        echo "        </div>";
        echo "    </div>";
        echo "</body>";
        echo "</html>";
        exit;
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Payment</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e8f4f8;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 2px solid #007bff;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,123,255,0.1);
            box-sizing: border-box;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
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


        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
    </style>
    <link rel="stylesheet" href="css/sdk-common.php">
    <script src="js/sdk-common.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/transactions.php">← Back to Transactions</a>
        </div>

        <h1>Refund Payment</h1>

        <div id="loading" class="loading">
            <div class="spinner"></div>
            <span>Loading payment details...</span>
        </div>

        <!-- Payment Details Table - Will be populated by JavaScript -->
        <div id="payment-details" style="display: none;"></div>

        <!-- Refund Form - Will be shown/hidden by JavaScript -->
        <form id="refund-form" method="POST" style="display: none;">
            <input type="hidden" id="payment-id-input" name="payment_id" value="<?php echo isset($paymentId) ? htmlspecialchars($paymentId) : ''; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="refund-amount">Refund Amount:</label>
            <input type="number" id="refund-amount" name="refund_amount" step="0.01" min="0.01" required>

            <button type="submit">Submit Refund</button>
        </form>

        <div id="refund-error" class="error-message" style="display: none;">Refund cannot be processed because the payment status is not successful.</div>

        <div class="links">
            <a href="/transactions.php">Back to Transactions</a>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Payment Refunds</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Method:</strong> <code>Refund()</code></p>
                <p><strong>Request:</strong> <code>RefundRequest</code></p>
                <p><strong>Response:</strong> <code>RefundResponse</code> (streaming)</p>
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
use Com\Kodypay\Grpc\Ecom\V1\RefundRequest;
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

// Step 3: Create RefundRequest and set required fields
$request = new RefundRequest();
$request->setStoreId('your-store-id');
$request->setPaymentId('payment-id-to-refund');
$request->setAmount(10.50); // Refund amount in major currency units

// Step 4: Optional fields
$request->setRefundReference('refund-ref-' . uniqid()); // Optional refund reference

// Step 5: Call Refund() method and get streaming response
$call = $client->Refund($request, $metadata);

// Step 6: Process streaming responses
foreach ($call->responses() as $response) {
    echo "Refund Status: " . $response->getStatus() . PHP_EOL;
    echo "Payment ID: " . $response->getPaymentId() . PHP_EOL;
    echo "Payment Transaction ID: " . $response->getPaymentTransactionId() . PHP_EOL;
    echo "Date Created: " . date('Y-m-d H:i:s', $response->getDateCreated()->getSeconds()) . PHP_EOL;
    echo "Total Paid Amount: " . number_format($response->getTotalPaidAmount(), 2) . PHP_EOL;
    echo "Total Amount Requested: " . number_format($response->getTotalAmountRequested(), 2) . PHP_EOL;
    echo "Total Amount Refunded: " . number_format($response->getTotalAmountRefunded(), 2) . PHP_EOL;
    echo "Remaining Amount: " . number_format($response->getRemainingAmount(), 2) . PHP_EOL;
    echo "---" . PHP_EOL;
}

// Step 7: Check for any errors
$status = $call->getStatus();
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.ecom.v1.KodyEcomPaymentsServiceGrpc;
import com.kodypay.grpc.ecom.v1.RefundRequest;
import com.kodypay.grpc.ecom.v1.RefundResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;
import java.util.Iterator;

public class RefundExample {
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

        // Step 3: Create RefundRequest and set required fields
        RefundRequest request = RefundRequest.newBuilder()
            .setStoreId("your-store-id")
            .setPaymentId("payment-id-to-refund")
            .setAmount(10.50) // Refund amount in major currency units
            .setRefundReference("refund-ref-" + System.currentTimeMillis()) // Optional
            .build();

        // Step 4: Call Refund() method and get streaming response
        Iterator&lt;RefundResponse&gt; responses = client.refund(request);

        // Step 5: Process streaming responses
        while (responses.hasNext()) {
            RefundResponse response = responses.next();

            System.out.println("Refund Status: " + response.getStatus());
            System.out.println("Payment ID: " + response.getPaymentId());
            System.out.println("Payment Transaction ID: " + response.getPaymentTransactionId());
            System.out.println("Date Created: " + response.getDateCreated());
            System.out.println("Total Paid Amount: " + String.format("%.2f", response.getTotalPaidAmount()));
            System.out.println("Total Amount Requested: " + String.format("%.2f", response.getTotalAmountRequested()));
            System.out.println("Total Amount Refunded: " + String.format("%.2f", response.getTotalAmountRefunded()));
            System.out.println("Remaining Amount: " + String.format("%.2f", response.getRemainingAmount()));
            System.out.println("---");
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

def process_refund():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create RefundRequest and set required fields
    request = kody_model.RefundRequest(
        store_id="your-store-id",
        payment_id="payment-id-to-refund",
        amount=10.50,  # Refund amount in major currency units
        refund_reference=f"refund-ref-{int(time.time())}"  # Optional
    )

    # Step 4: Call Refund() method and get streaming response
    responses = client.Refund(request, metadata=metadata)

    # Step 5: Process streaming responses
    for response in responses:
        print(f"Refund Status: {response.status}")
        print(f"Payment ID: {response.payment_id}")
        print(f"Payment Transaction ID: {response.payment_transaction_id}")
        print(f"Date Created: {response.date_created}")
        print(f"Total Paid Amount: {response.total_paid_amount:.2f}")
        print(f"Total Amount Requested: {response.total_amount_requested:.2f}")
        print(f"Total Amount Refunded: {response.total_amount_refunded:.2f}")
        print(f"Remaining Amount: {response.remaining_amount:.2f}")
        print("---")

if __name__ == "__main__":
    try:
        process_refund()
    except grpc.RpcError as e:
        print(f"gRPC Error: {e.code()} - {e.details()}")
    except Exception as e:
        print(f"Exception: {e}")</code></pre>
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

        // Step 4: Create RefundRequest and set required fields
        var request = new RefundRequest
        {
            StoreId = "your-store-id",
            PaymentId = "payment-id-to-refund",
            Amount = 10.50, // Refund amount in major currency units
            RefundReference = $"refund-ref-{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}" // Optional
        };

        try
        {
            // Step 5: Call Refund() method and get streaming response
            using var call = client.Refund(request, metadata);

            // Step 6: Process streaming responses
            await foreach (var response in call.ResponseStream.ReadAllAsync())
            {
                Console.WriteLine($"Refund Status: {response.Status}");
                Console.WriteLine($"Payment ID: {response.PaymentId}");
                Console.WriteLine($"Payment Transaction ID: {response.PaymentTransactionId}");
                Console.WriteLine($"Date Created: {response.DateCreated}");
                Console.WriteLine($"Total Paid Amount: {response.TotalPaidAmount:F2}");
                Console.WriteLine($"Total Amount Requested: {response.TotalAmountRequested:F2}");
                Console.WriteLine($"Total Amount Refunded: {response.TotalAmountRefunded:F2}");
                Console.WriteLine($"Remaining Amount: {response.RemainingAmount:F2}");
                Console.WriteLine("---");
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
        const urlParams = new URLSearchParams(window.location.search);
        const paymentId = urlParams.get('payment_id');

        if (paymentId) {
            fetchPaymentDetails(paymentId);
        }
    });

    function fetchPaymentDetails(paymentId) {
        const loadingElement = document.getElementById('loading');
        if (loadingElement) loadingElement.style.display = 'block';

        fetch(`api/payment_details.php?payment_id=${paymentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load payment details');
                }

                displayPaymentDetails(data);

                const loadingElement = document.getElementById('loading');
                const paymentDetailsElement = document.getElementById('payment-details');
                const refundFormElement = document.getElementById('refund-form');
                const refundErrorElement = document.getElementById('refund-error');

                if (loadingElement) loadingElement.style.display = 'none';
                if (paymentDetailsElement) paymentDetailsElement.style.display = 'block';

                console.log("Payment status:", data.status);

                // Show/hide refund form based on payment status
                if (data.status === 'success' || data.statusText === 'SUCCESS') {
                    console.log("Showing refund form");
                    if (refundFormElement) refundFormElement.style.display = 'block';
                    if (refundErrorElement) refundErrorElement.style.display = 'none';

                    const paymentIdInput = document.getElementById('payment-id-input');
                    if (paymentIdInput) paymentIdInput.value = data.paymentId;
                } else {
                    console.log("Hiding refund form, status is not success");
                    if (refundFormElement) refundFormElement.style.display = 'none';
                    if (refundErrorElement) refundErrorElement.style.display = 'block';
                }
            })
            .catch(error => {
                console.error("Error fetching payment details:", error);

                const loadingElement = document.getElementById('loading');
                const paymentDetailsElement = document.getElementById('payment-details');

                if (loadingElement) loadingElement.style.display = 'none';
                if (paymentDetailsElement) {
                    paymentDetailsElement.innerHTML =
                        `<p style="color: red">Error loading payment details: ${error.message}</p>`;
                    paymentDetailsElement.style.display = 'block';
                }
            });
    }

    function displayPaymentDetails(data) {
        const paymentDetailsElement = document.getElementById('payment-details');
        if (!paymentDetailsElement) return;

        let html = `
        <table>
            <tbody>
            <!-- Basic Details -->
            <tr><th colspan="2" style="background-color: #e0e0e0;">Basic Details</th></tr>
            <tr>
                <th>Payment ID</th>
                <td>${data.paymentId || 'N/A'}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>${data.statusText || 'N/A'}</td>
            </tr>
            <tr>
                <th>Date Created</th>
                <td>${data.dateCreated || 'N/A'}</td>
            </tr>`;

        if (data.datePaid) {
            html += `
            <tr>
                <th>Date Paid</th>
                <td>${data.datePaid}</td>
            </tr>`;
        }

        html += `
            <!-- Payment Data -->
            <tr><th colspan="2" style="background-color: #e0e0e0;">Payment Data</th></tr>
            <tr>
                <th>PSP Reference</th>
                <td>${data.paymentData?.pspReference || 'N/A'}</td>
            </tr>
            <tr>
                <th>Payment Method Variant</th>
                <td>${data.paymentData?.paymentMethodVariant || 'N/A'}</td>
            </tr>
            <tr>
                <th>Auth Status</th>
                <td>${data.paymentData?.authStatus || 'N/A'}</td>
            </tr>
            <tr>
                <th>Auth Status Date</th>
                <td>${data.paymentData?.authStatusDate || 'N/A'}</td>
            </tr>`;

        // Payment Card section (conditional)
        if (data.paymentData?.paymentCard) {
            html += `
            <!-- Payment Card -->
            <tr><th colspan="2" style="background-color: #e0e0e0;">Payment Card</th></tr>
            <tr>
                <th>Card Last 4 Digits</th>
                <td>${data.paymentData.paymentCard.cardLast4Digits || 'N/A'}</td>
            </tr>
            <tr>
                <th>Card Auth Code</th>
                <td>${data.paymentData.paymentCard.authCode || 'N/A'}</td>
            </tr>`;
        }

        // Payment Wallet section (conditional)
        if (data.paymentData?.paymentWallet) {
            html += `
            <!-- Payment Wallet -->
            <tr><th colspan="2" style="background-color: #e0e0e0;">Payment Wallet</th></tr>
            <tr>
                <th>Payment Link ID</th>
                <td>${data.paymentData.paymentWallet.paymentLinkId || 'N/A'}</td>
            </tr>
            <tr>
                <th>Wallet Card Last 4 Digits</th>
                <td>${data.paymentData.paymentWallet.cardLast4Digits || 'N/A'}</td>
            </tr>`;
        }

        html += `
            <!-- Sale Data -->
            <tr><th colspan="2" style="background-color: #e0e0e0;">Sale Data</th></tr>
            <tr>
                <th>Order ID</th>
                <td>${data.saleData?.orderId || 'N/A'}</td>
            </tr>
            <tr>
                <th>Payment Reference</th>
                <td>${data.saleData?.paymentReference || 'N/A'}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>${data.saleData?.amount || 'N/A'} ${data.saleData?.currency || ''}</td>
            </tr>`;

        if (data.saleData?.orderMetadata) {
            html += `
            <tr>
                <th>Order Metadata</th>
                <td>${data.saleData.orderMetadata}</td>
            </tr>`;
        }

        html += `
            </tbody>
        </table>`;

        paymentDetailsElement.innerHTML = html;
    }

</script>
<script src="js/bubble.php"></script>
</body>
</html>
