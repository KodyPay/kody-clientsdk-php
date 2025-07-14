<?php
$expectedStatus = isset($_GET['status']) ? strtolower($_GET['status']) : "";
$paymentReference = isset($_GET['paymentReference']) ? $_GET['paymentReference'] : "";

if (empty($paymentReference)) {
    $message = "Missing payment reference.";
    $class = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
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

        .message {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px 0;
            text-align: center;
            border-radius: 6px;
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

        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .failure, .failed { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .expired { background-color: #fff3cd; color: #856404; border-color: #ffeaa7; }
        .error { background-color: #fff3cd; color: #856404; border-color: #ffeaa7; }
        .unknown { background-color: #e2e3e5; color: #383d41; border-color: #d1ecf1; }
        .pending { background-color: #cce5ff; color: #004085; border-color: #b8daff; }
        .cancelled { background-color: #e2e3e5; color: #383d41; border-color: #d1ecf1; }

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

        .details-container {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
            display: none;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .details-container h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td, .details-table th {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .details-table th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        .details-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .details-table tr:hover {
            background-color: #e8f4f8;
        }

        /* SDK Section Styles */
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
    </style>
    <script src="js/bubble.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/checkout.php">← Back to Checkout</a>
        </div>

        <h1>Payment Result</h1>

        <?php if (empty($paymentReference)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php else: ?>
            <!-- Initial status message based on status parameter -->
            <div id="status-message" class="message <?php echo htmlspecialchars($expectedStatus); ?>">
                <?php
                if ($expectedStatus == 'error'):
                    echo "Payment is error. Awaiting confirmation.";
                elseif ($expectedStatus == 'success'):
                    echo "Payment was successful. Awaiting confirmation.";
                else:
                    echo htmlspecialchars(ucfirst($expectedStatus) . " payment status.");
                endif;
                ?>
            </div>

            <!-- Loading indicator -->
            <div id="loading" class="loading">
                <div class="spinner"></div>
                <span>Verifying payment details...</span>
            </div>

            <!-- Payment details container (hidden initially) -->
            <div id="payment-details" class="details-container">
                <h3>Payment Details</h3>
                <table class="details-table">
                    <tbody id="details-body">
                        <!-- Will be populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="/checkout.php">New online payment</a> | <a href="/index.php">Main menu</a> | <a href="/transactions.php">List all transactions</a>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Get Payment Details</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyEcomPaymentsService</code></p>
                <p><strong>Method:</strong> <code>GetPayments()</code></p>
                <p><strong>Request:</strong> <code>GetPaymentsRequest</code></p>
                <p><strong>Response:</strong> <code>GetPaymentsResponse</code></p>
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
use Com\Kodypay\Grpc\Ecom\V1\GetPaymentsRequest;
use Com\Kodypay\Grpc\Ecom\V1\GetPaymentsRequest\Filter;
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

// Step 3: Create GetPaymentsRequest and set required fields
$request = new GetPaymentsRequest();
$request->setStoreId('your-store-id');

// Step 4: Filter by payment reference to get specific payment
$filter = new Filter();
$filter->setPaymentReference('payment-ref-123'); // Payment reference from return URL
$request->setFilter($filter);

// Step 5: Call GetPayments() method and wait for response
list($response, $status) = $client->GetPayments($request, $metadata)->wait();

// Step 6: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 7: Process response
if ($response->hasResponse()) {
    $responseData = $response->getResponse();
    $payments = $responseData->getPayments();

    if (count($payments) > 0) {
        $payment = $payments[0]; // Get the first (and likely only) payment

        echo "Payment ID: " . $payment->getPaymentId() . PHP_EOL;
        echo "Status: " . $payment->getStatus() . PHP_EOL;
        echo "Date Created: " . $payment->getDateCreated()->toDateTime()->format('Y-m-d H:i:s') . PHP_EOL;

        if ($payment->hasSaleData()) {
            $saleData = $payment->getSaleData();
            echo "Amount: " . $saleData->getAmountMinorUnits() . " " . $saleData->getCurrency() . PHP_EOL;
            echo "Order ID: " . $saleData->getOrderId() . PHP_EOL;
            echo "Payment Reference: " . $saleData->getPaymentReference() . PHP_EOL;
        }

        if ($payment->hasPaymentData()) {
            $paymentData = $payment->getPaymentData();
            echo "PSP Reference: " . $paymentData->getPspReference() . PHP_EOL;
            echo "Payment Method: " . $paymentData->getPaymentMethodVariant() . PHP_EOL;
        }
    } else {
        echo "No payment found with the given reference" . PHP_EOL;
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
import com.kodypay.grpc.ecom.v1.GetPaymentsRequest;
import com.kodypay.grpc.ecom.v1.GetPaymentsResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class GetPaymentDetailsExample {
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

        // Step 3: Create GetPaymentsRequest and set required fields
        var filter = GetPaymentsRequest.Filter.newBuilder()
            .setPaymentReference("payment-ref-123") // Payment reference from return URL
            .build();

        GetPaymentsRequest request = GetPaymentsRequest.newBuilder()
            .setStoreId("your-store-id")
            .setFilter(filter)
            .build();

        // Step 4: Call GetPayments() method and get response
        GetPaymentsResponse response = client.getPayments(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            var responseData = response.getResponse();

            if (responseData.getPaymentsCount() > 0) {
                var payment = responseData.getPayments(0); // Get the first payment

                System.out.println("Payment ID: " + payment.getPaymentId());
                System.out.println("Status: " + payment.getStatus());
                System.out.println("Date Created: " + payment.getDateCreated());

                if (payment.hasSaleData()) {
                    var saleData = payment.getSaleData();
                    System.out.println("Amount: " + saleData.getAmountMinorUnits() + " " + saleData.getCurrency());
                    System.out.println("Order ID: " + saleData.getOrderId());
                    System.out.println("Payment Reference: " + saleData.getPaymentReference());
                }

                if (payment.hasPaymentData()) {
                    var paymentData = payment.getPaymentData();
                    System.out.println("PSP Reference: " + paymentData.getPspReference());
                    System.out.println("Payment Method: " + paymentData.getPaymentMethodVariant());
                }
            } else {
                System.out.println("No payment found with the given reference");
            }
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

def get_payment_details():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create GetPaymentsRequest and set required fields
    filter_obj = kody_model.GetPaymentsRequest.Filter(
        payment_reference="payment-ref-123"  # Payment reference from return URL
    )

    request = kody_model.GetPaymentsRequest(
        store_id="your-store-id",
        filter=filter_obj
    )

    # Step 4: Call GetPayments() method and get response
    response = client.GetPayments(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        response_data = response.response

        if len(response_data.payments) > 0:
            payment = response_data.payments[0]  # Get the first payment

            print(f"Payment ID: {payment.payment_id}")
            print(f"Status: {payment.status}")
            print(f"Date Created: {payment.date_created}")

            if payment.HasField("sale_data"):
                sale_data = payment.sale_data
                print(f"Amount: {sale_data.amount_minor_units} {sale_data.currency}")
                print(f"Order ID: {sale_data.order_id}")
                print(f"Payment Reference: {sale_data.payment_reference}")

            if payment.HasField("payment_data"):
                payment_data = payment.payment_data
                print(f"PSP Reference: {payment_data.psp_reference}")
                print(f"Payment Method: {payment_data.payment_method_variant}")
        else:
            print("No payment found with the given reference")
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    get_payment_details()</code></pre>
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

        // Step 4: Create GetPaymentsRequest and set required fields
        var request = new GetPaymentsRequest
        {
            StoreId = "your-store-id",
            Filter = new GetPaymentsRequest.Types.Filter
            {
                PaymentReference = "payment-ref-123" // Payment reference from return URL
            }
        };

        try
        {
            // Step 5: Call GetPayments() method and get response
            var response = await client.GetPaymentsAsync(request, metadata);

            // Step 6: Process response
            if (response.ResponseCase == GetPaymentsResponse.ResponseOneofCase.Response)
            {
                var responseData = response.Response;

                if (responseData.Payments.Count > 0)
                {
                    var payment = responseData.Payments[0]; // Get the first payment

                    Console.WriteLine($"Payment ID: {payment.PaymentId}");
                    Console.WriteLine($"Status: {payment.Status}");
                    Console.WriteLine($"Date Created: {payment.DateCreated}");

                    if (payment.SaleData != null)
                    {
                        var saleData = payment.SaleData;
                        Console.WriteLine($"Amount: {saleData.AmountMinorUnits} {saleData.Currency}");
                        Console.WriteLine($"Order ID: {saleData.OrderId}");
                        Console.WriteLine($"Payment Reference: {saleData.PaymentReference}");
                    }

                    if (payment.PaymentData != null)
                    {
                        var paymentData = payment.PaymentData;
                        Console.WriteLine($"PSP Reference: {paymentData.PspReference}");
                        Console.WriteLine($"Payment Method: {paymentData.PaymentMethodVariant}");
                    }
                }
                else
                {
                    Console.WriteLine("No payment found with the given reference");
                }
            }
            else if (response.ResponseCase == GetPaymentsResponse.ResponseOneofCase.Error)
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

<?php if (!empty($paymentReference)): ?>
<script>
    const RETRY_DELAY = 2000; // ms
    const MAX_RETRIES = 30;
    let retryCount = 0;

    const statusMessage = document.getElementById('status-message');
    const loadingElement = document.getElementById('loading');
    const detailsContainer = document.getElementById('payment-details');
    const detailsBody = document.getElementById('details-body');

    const paymentReference = "<?php echo htmlspecialchars($paymentReference); ?>";
    const expectedStatus = "<?php echo htmlspecialchars($expectedStatus); ?>";

    function updateStatus(status, message) {
        statusMessage.textContent = message;
        statusMessage.className = "message " + status;
    }

    function displayPaymentDetails(data) {
        // Main payment details
        const fields = [
            { key: 'paymentId', label: 'Payment ID' },
            { key: 'paymentReference', label: 'Payment Reference' },
            { key: 'status', label: 'Status', value: data.statusText },
            { key: 'rawStatus', label: 'Raw Status' },
            { key: 'dateCreated', label: 'Date Created' },
            { key: 'datePaid', label: 'Date Paid' }
        ];

        // Payment data fields
        const paymentDataFields = [
            { key: 'pspReference', label: 'PSP Reference' },
            { key: 'paymentMethodVariant', label: 'Payment Method' },
            { key: 'authStatus', label: 'Auth Status' },
            { key: 'authStatusDate', label: 'Auth Status Date' }
        ];

        // Sale data fields
        const saleDataFields = [
            { key: 'amount', label: 'Amount' },
            { key: 'currency', label: 'Currency' },
            { key: 'orderId', label: 'Order ID' },
            { key: 'orderMetadata', label: 'Order Metadata' }
        ];

        // Wallet data fields
        const walletDataFields = [
            { key: 'paymentLinkId', label: 'Payment Link ID' },
            { key: 'cardLast4Digits', label: 'Card Last 4 Digits' }
        ];

        detailsBody.innerHTML = '';

        // Add main fields
        fields.forEach(field => {
            if (data[field.key] !== undefined || field.value !== undefined) {
                const row = document.createElement('tr');
                const labelCell = document.createElement('th');
                labelCell.textContent = field.label;
                const valueCell = document.createElement('td');
                valueCell.textContent = field.value !== undefined ? field.value : data[field.key];
                row.appendChild(labelCell);
                row.appendChild(valueCell);
                detailsBody.appendChild(row);
            }
        });

        // Add payment data fields
        if (data.paymentData) {
            paymentDataFields.forEach(field => {
                if (data.paymentData[field.key] !== undefined) {
                    const row = document.createElement('tr');
                    const labelCell = document.createElement('th');
                    labelCell.textContent = field.label;
                    const valueCell = document.createElement('td');
                    valueCell.textContent = data.paymentData[field.key];
                    row.appendChild(labelCell);
                    row.appendChild(valueCell);
                    detailsBody.appendChild(row);
                }
            });
        }

        // Add sale data fields
        if (data.saleData) {
            saleDataFields.forEach(field => {
                if (data.saleData[field.key] !== undefined) {
                    const row = document.createElement('tr');
                    const labelCell = document.createElement('th');
                    labelCell.textContent = field.label;
                    const valueCell = document.createElement('td');
                    valueCell.textContent = data.saleData[field.key];
                    row.appendChild(labelCell);
                    row.appendChild(valueCell);
                    detailsBody.appendChild(row);
                }
            });
        }

        // Add wallet data fields if available
        if (data.paymentData && data.paymentData.paymentWallet) {
            walletDataFields.forEach(field => {
                if (data.paymentData.paymentWallet[field.key] !== undefined) {
                    const row = document.createElement('tr');
                    const labelCell = document.createElement('th');
                    labelCell.textContent = field.label;
                    const valueCell = document.createElement('td');
                    valueCell.textContent = data.paymentData.paymentWallet[field.key];
                    row.appendChild(labelCell);
                    row.appendChild(valueCell);
                    detailsBody.appendChild(row);
                }
            });
        }

        detailsContainer.style.display = 'block';
    }

    function fetchPaymentStatus() {
        fetch(`api/payment_details.php?paymentReference=${paymentReference}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const status = data.status || 'unknown';
                    const statusText = data.statusText || '';

                    displayPaymentDetails(data);

                    // Always keep retrying for pending status
                    if (status === 'pending') {
                        if (expectedStatus === 'error') {
                            updateStatus('pending', "Payment is error. Awaiting confirmation.");
                        } else if (expectedStatus === 'success') {
                            updateStatus('pending', "Payment was successful. Awaiting confirmation.");
                        } else {
                            updateStatus('pending', "Payment is being processed. Please wait...");
                        }

                        // Always retry for pending status
                        retryCount++;
                        setTimeout(fetchPaymentStatus, RETRY_DELAY);
                        return;
                    }

                    // For non-pending statuses, show final result
                    loadingElement.style.display = 'none';

                    if (status === 'success') {
                        updateStatus('success', "Payment completed successfully!");
                    } else if (status === 'error' || status === 'failure' || status === 'failed') {
                        updateStatus(status, `Payment failed: ${statusText}`);
                    } else {
                        updateStatus(status, `Payment status: ${statusText}`);
                    }
                } else {
                    // No data available yet, retry
                    if (retryCount < MAX_RETRIES) {
                        retryCount++;
                        setTimeout(fetchPaymentStatus, RETRY_DELAY);
                    } else {
                        // Max retries reached
                        loadingElement.style.display = 'none';

                        if (expectedStatus === 'error') {
                            updateStatus('error', "Payment verification incomplete. Please try again later.");
                        } else if (expectedStatus === 'success') {
                            updateStatus('pending', "Payment submitted but confirmation is pending. Please try again later.");
                        } else {
                            updateStatus('unknown', "Payment details not found after multiple attempts.");
                        }
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching payment details:", error);

                if (retryCount < MAX_RETRIES) {
                    retryCount++;
                    setTimeout(fetchPaymentStatus, RETRY_DELAY);
                } else {
                    loadingElement.style.display = 'none';
                    updateStatus('unknown', "Error communicating with payment service. Please try again later.");
                }
            });
    }

    fetchPaymentStatus();
</script>
<?php endif; ?>

<!-- SDK section functions - globally accessible -->
<script>
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

    // Initialize SDK section - show PHP tab by default
    document.addEventListener('DOMContentLoaded', function() {
        showTab('php');
    });
</script>
</body>
</html>
