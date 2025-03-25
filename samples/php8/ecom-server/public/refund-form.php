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
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }

                h1 {
                    margin-top: 0;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                th, td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }

                th {
                    background-color: #f5f5f5;
                    font-weight: bold;
                }

                tr:hover {
                    background-color: #f9f9f9;
                }
              </style>";
        echo "</head>";
        echo "<body>";
        echo "<h1>Refund Result</h1>";

        if (empty($refunds)) {
            echo "<p style='font-weight: bold; color: red;'>Refund Error: Exceeded the maximum refund amount.</p>";
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
                echo "</table><br>";
            }
        }
        echo "<a href='/refund-form.php?payment_id=" . htmlspecialchars($paymentId) . "'>Back to Refund Form</a> | <a href='/transactions.php'>Back to Transactions</a>";
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
    <title>Refund Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        form {
            margin-top: 20px;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
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
    </style>
</head>
<body>
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

<div id="refund-error" style="color: red; display: none;">Refund cannot be processed because the payment status is not successful.</div>

<p><a href="/transactions.php">Back to Transactions</a></p>

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
</body>
</html>
