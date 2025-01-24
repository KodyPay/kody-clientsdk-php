<?php
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\RefundRequest;
use Grpc\ChannelCredentials;

header("Content-Type: text/html");

$refundStatusMapping = [
    0 => 'UNKNOWN',
    1 => 'REQUESTED',
    2 => 'SUCCESS',
    3 => 'FAILED',
    4 => 'PARTIAL_SUCCESS',
];

try {
    // Check if data is passed through GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['payment_id'], $_GET['order_id'], $_GET['status'], $_GET['date_created'], $_GET['date_paid'])) {
            throw new Exception("Invalid access: Required payment details are missing.");
        }

        $paymentId = $_GET['payment_id'];
        $orderId = $_GET['order_id'];
        $status = $_GET['status'];
        $dateCreated = $_GET['date_created'];
        $datePaid = $_GET['date_paid'];
    }

    // Process refund form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['payment_id'], $_POST['refund_amount'])) {
            throw new Exception("Invalid input: Payment ID and refund amount are required.");
        }

        $paymentId = $_POST['payment_id'];
        $refundAmount = $_POST['refund_amount'];

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
                'status_text' => isset($refundStatusMapping[$response->getStatus()])
                    ? $refundStatusMapping[$response->getStatus()]
                    : 'UNKNOWN',
                'payment_id' => $response->getPaymentId(),
                'payment_transaction_id' => $response->getpaymentTransactionId(),
                'date_created' => date('Y-m-d H:i:s', $response->getDateCreated()->getSeconds()),
                'total_paid_amount' => $response->getTotalPaidAmount(),
                'total_amount_requested' => $response->getTotalAmountRequested(),
                'total_amount_refunded' => $response->getTotalAmountRefunded(),
                'remaining_amount' => $response->getRemainingAmount(),
            ];
        }

        // Output the refund results
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
            // Display refund error if no refunds are found
            echo "<p style='font-weight: bold;'>Refund Error: No refund data available.</p>";
        } else {
            // Display refund details in a consistent table format
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
    </style>
</head>
<body>
<h1>Refund Payment</h1>

<!-- Payment Details Table -->
<table>
    <tbody>
    <tr>
        <th>Payment ID</th>
        <td><?php echo htmlspecialchars($paymentId); ?></td>
    </tr>
    <tr>
        <th>Order ID</th>
        <td><?php echo htmlspecialchars($orderId); ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?php echo htmlspecialchars($status); ?></td>
    </tr>
    <tr>
        <th>Date Created</th>
        <td><?php echo htmlspecialchars($dateCreated); ?></td>
    </tr>
    <tr>
        <th>Date Paid</th>
        <td><?php echo htmlspecialchars($datePaid); ?></td>
    </tr>
    </tbody>
</table>

<!-- Refund Form -->
<?php if ($status === 'SUCCESS'): ?>
    <form method="POST">
        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($paymentId); ?>">
        <label for="refund-amount">Refund Amount:</label>
        <input type="number" id="refund-amount" name="refund_amount" step="0.01" min="0.01" required>
        <button type="submit">Submit Refund</button>
    </form>
<?php else: ?>
    <p style="color: red;">Refund cannot be processed because the payment status is not successful.</p>
<?php endif; ?>

</body>
</html>