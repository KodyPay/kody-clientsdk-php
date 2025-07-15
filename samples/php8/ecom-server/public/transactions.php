<?php
$functions = require_once __DIR__ . '/functions.php';
$currentPage = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transactions</title>
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

        .total-count {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
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

        .status-SUCCESS {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-FAILED {
            color: #e74c3c;
            font-weight: bold;
        }

        .status-PENDING {
            color: #ff9800;
            font-weight: bold;
        }

        .refund-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .refund-btn:hover {
            background-color: #45a049;
        }

        .refund-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination a:hover {
            background-color: #f8f9fa;
        }

        .pagination .disabled {
            color: #aaa;
            pointer-events: none;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
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

        .no-payments {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
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
    <script src="js/sdk-common.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Payment Transactions</h1>

        <div id="total-count" class="total-count">Loading transactions...</div>

        <div id="payments-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Loading payment data...</span>
            </div>
        </div>

        <div id="pagination" class="pagination"></div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Get Payments</h2>

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
use Com\Kodypay\Grpc\Ecom\V1\GetPaymentsRequest\PageCursor;
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

// Step 4: Set pagination (optional)
$pageCursor = new PageCursor();
$pageCursor->setPage(0); // First page
$pageCursor->setPageSize(20); // 20 payments per page
$request->setPageCursor($pageCursor);

// Step 5: Set filters (optional)
$filter = new Filter();
$filter->setOrderId('order-123'); // Filter by specific order ID
// You can also filter by created_before timestamp
$request->setFilter($filter);

// Step 6: Call GetPayments() method and wait for response
list($response, $status) = $client->GetPayments($request, $metadata)->wait();

// Step 7: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 8: Process response
if ($response->hasResponse()) {
    $responseData = $response->getResponse();
    $payments = $responseData->getPayments();

    echo "Found " . $responseData->getTotal() . " total payments:" . PHP_EOL;
    echo "Showing " . count($payments) . " payments on this page:" . PHP_EOL;

    foreach ($payments as $payment) {
        echo "Payment ID: " . $payment->getPaymentId() . PHP_EOL;
        echo "Status: " . $payment->getStatus() . PHP_EOL;
        echo "Created: " . $payment->getDateCreated()->toDateTime()->format('Y-m-d H:i:s') . PHP_EOL;

        if ($payment->hasSaleData()) {
            $saleData = $payment->getSaleData();
            echo "Amount: " . $saleData->getAmountMinorUnits() . " " . $saleData->getCurrency() . PHP_EOL;
            echo "Order ID: " . $saleData->getOrderId() . PHP_EOL;
        }
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
import com.kodypay.grpc.ecom.v1.GetPaymentsRequest;
import com.kodypay.grpc.ecom.v1.GetPaymentsResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class GetPaymentsExample {
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
        var pageCursor = GetPaymentsRequest.PageCursor.newBuilder()
            .setPage(0) // First page
            .setPageSize(20) // 20 payments per page
            .build();

        var filter = GetPaymentsRequest.Filter.newBuilder()
            .setOrderId("order-123") // Filter by specific order ID
            .build();

        GetPaymentsRequest request = GetPaymentsRequest.newBuilder()
            .setStoreId("your-store-id")
            .setPageCursor(pageCursor)
            .setFilter(filter)
            .build();

        // Step 4: Call GetPayments() method and get response
        GetPaymentsResponse response = client.getPayments(request);

        // Step 5: Process response
        if (response.hasResponse()) {
            var responseData = response.getResponse();
            System.out.println("Found " + responseData.getTotal() + " total payments:");
            System.out.println("Showing " + responseData.getPaymentsCount() + " payments on this page:");

            responseData.getPaymentsList().forEach(payment -> {
                System.out.println("Payment ID: " + payment.getPaymentId());
                System.out.println("Status: " + payment.getStatus());
                System.out.println("Created: " + payment.getDateCreated());

                if (payment.hasSaleData()) {
                    var saleData = payment.getSaleData();
                    System.out.println("Amount: " + saleData.getAmountMinorUnits() + " " + saleData.getCurrency());
                    System.out.println("Order ID: " + saleData.getOrderId());
                }
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

def get_payments():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyEcomPaymentsServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create GetPaymentsRequest and set required fields
    page_cursor = kody_model.GetPaymentsRequest.PageCursor(
        page=0,  # First page
        page_size=20  # 20 payments per page
    )

    filter_obj = kody_model.GetPaymentsRequest.Filter(
        order_id="order-123"  # Filter by specific order ID
    )

    request = kody_model.GetPaymentsRequest(
        store_id="your-store-id",
        page_cursor=page_cursor,
        filter=filter_obj
    )

    # Step 4: Call GetPayments() method and get response
    response = client.GetPayments(request, metadata=metadata)

    # Step 5: Process response
    if response.HasField("response"):
        response_data = response.response
        print(f"Found {response_data.total} total payments:")
        print(f"Showing {len(response_data.payments)} payments on this page:")

        for payment in response_data.payments:
            print(f"Payment ID: {payment.payment_id}")
            print(f"Status: {payment.status}")
            print(f"Created: {payment.date_created}")

            if payment.HasField("sale_data"):
                sale_data = payment.sale_data
                print(f"Amount: {sale_data.amount_minor_units} {sale_data.currency}")
                print(f"Order ID: {sale_data.order_id}")
            print("---")
    elif response.HasField("error"):
        error = response.error
        print(f"API Error: {error.message}")

if __name__ == "__main__":
    get_payments()</code></pre>
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
            PageCursor = new GetPaymentsRequest.Types.PageCursor
            {
                Page = 0, // First page
                PageSize = 20 // 20 payments per page
            },
            Filter = new GetPaymentsRequest.Types.Filter
            {
                OrderId = "order-123" // Filter by specific order ID
            }
        };

        // Step 5: Call GetPayments() method and get response
        var response = await client.GetPaymentsAsync(request, metadata);

        // Step 6: Process response
        if (response.ResponseCase == GetPaymentsResponse.ResponseOneofCase.Response)
        {
            var responseData = response.Response;
            Console.WriteLine($"Found {responseData.Total} total payments:");
            Console.WriteLine($"Showing {responseData.Payments.Count} payments on this page:");

            foreach (var payment in responseData.Payments)
            {
                Console.WriteLine($"Payment ID: {payment.PaymentId}");
                Console.WriteLine($"Status: {payment.Status}");
                Console.WriteLine($"Created: {payment.DateCreated}");

                if (payment.SaleData != null)
                {
                    var saleData = payment.SaleData;
                    Console.WriteLine($"Amount: {saleData.AmountMinorUnits} {saleData.Currency}");
                    Console.WriteLine($"Order ID: {saleData.OrderId}");
                }
                Console.WriteLine("---");
            }
        }
        else if (response.ResponseCase == GetPaymentsResponse.ResponseOneofCase.Error)
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
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = <?php echo $currentPage; ?>;
            const pageSize = 16;

            loadPayments(currentPage);

            function loadPayments(page) {
                document.getElementById('payments-container').innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        <span>Loading payment data...</span>
                    </div>`;
                document.getElementById('pagination').innerHTML = '';
                document.getElementById('total-count').textContent = 'Loading transactions...';

                // Fetch payments from API
                fetch('/api/payments.php?page=' + page + '&pageSize=' + pageSize)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error, status = ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            renderPayments(data.payments, data.pagination);
                        } else {
                            showError(data.errorMessage || 'Failed to load payments');
                        }
                    })
                    .catch(error => {
                        showError('Error: ' + error.message);
                    });
            }

            function renderPayments(payments, pagination) {
                // Update pagination display
                document.getElementById('total-count').textContent =
                    'Total Transactions: ' + pagination.totalItems +
                    ' (Page ' + (pagination.currentPage + 1) + ' of ' + pagination.totalPages + ')';

                if (payments.length === 0) {
                    document.getElementById('payments-container').innerHTML = '<div class="no-payments">No payments found</div>';
                    return;
                }

                let html = `
                    <div class="table-container">
                        <table id="payments-table">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Reference</th>
                                    <th>Order ID</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Paid Date</th>
                                    <th>Paid Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                payments.forEach(payment => {
                    const isSuccess = payment.status_text === 'SUCCESS';

                    html += `
                        <tr data-payment-id="${payment.payment_id}" data-status="${payment.status_text}">
                            <td>${payment.payment_id}</td>
                            <td>${payment.payment_reference}</td>
                            <td>${payment.order_id}</td>
                            <td class="status-${payment.status_text}">${payment.status_text}</td>
                            <td>${payment.date_created || 'N/A'}</td>
                            <td>${payment.date_paid || 'N/A'}</td>
                            <td class="paid-amount">-</td>
                            <td>
                                ${isSuccess ?
                                    `<form action="refund-form.php" method="GET" style="margin: 0;">
                                        <input type="hidden" name="payment_id" value="${payment.payment_id}">
                                        <button type="submit" class="refund-btn">Refund</button>
                                    </form>` : '-'}
                            </td>
                        </tr>
                    `;
                });

                html += `</tbody></table></div>`;
                document.getElementById('payments-container').innerHTML = html;

                renderPagination(pagination);

                // Load amounts for successful payments only
                const rows = document.querySelectorAll('#payments-table tbody tr[data-status="SUCCESS"]');
                rows.forEach(row => {
                    const paymentId = row.getAttribute('data-payment-id');
                    if (paymentId) {
                        fetchPaymentDetails(paymentId, row);
                    }
                });
            }

            function renderPagination(pagination) {
                let html = '';

                if (pagination.currentPage > 0) {
                    html += `<a href="?page=${pagination.currentPage - 1}">Previous</a>`;
                } else {
                    html += `<span class="disabled">Previous</span>`;
                }

                html += `<span class="active">${pagination.currentPage + 1}</span>`;

                if (pagination.currentPage < pagination.totalPages - 1) {
                    html += `<a href="?page=${pagination.currentPage + 1}">Next</a>`;
                } else {
                    html += `<span class="disabled">Next</span>`;
                }

                document.getElementById('pagination').innerHTML = html;

                // Add click handlers for pagination
                document.querySelectorAll('#pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const newPage = url.searchParams.get('page');
                        loadPayments(parseInt(newPage));
                        window.history.pushState({}, '', `?page=${newPage}`);
                    });
                });
            }

            function fetchPaymentDetails(paymentId, row) {
                fetch('/api/payment_details.php?payment_id=' + encodeURIComponent(paymentId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error, status = ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.saleData && data.saleData.amount && data.saleData.currency) {
                            const amountText = data.saleData.amount + ' ' + data.saleData.currency;
                            row.querySelector('.paid-amount').textContent = amountText;
                        } else {
                            row.querySelector('.paid-amount').textContent = 'N/A';
                        }
                    })
                    .catch(error => {
                        row.querySelector('.paid-amount').textContent = 'Error';
                    });
            }

            function showError(message) {
                document.getElementById('payments-container').innerHTML =
                    `<div class="error-message">${escapeHtml(message)}</div>`;
                document.getElementById('total-count').textContent = 'Error loading transactions';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

        });

    </script>
</body>
</html>
