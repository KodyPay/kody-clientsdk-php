<?php
$config = require __DIR__ . '/config.php';


use Com\Kodypay\Grpc\Ecom\V1\KodyEcomPaymentsServiceClient;
use Com\Kodypay\Grpc\Ecom\V1\GetPaymentsRequest;
use Com\Kodypay\Grpc\Sdk\Common\PageCursor;
use Grpc\ChannelCredentials;

function getStatusText($statusCode) {
    $statuses = [
        0 => 'PENDING',
        1 => 'SUCCESS',
        2 => 'FAILED',
        3 => 'CANCELLED',
        4 => 'EXPIRED'
    ];
    return $statuses[$statusCode] ?? 'UNKNOWN';
}

// Get current page from query parameter
$currentPage = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
$pageSize = 20;

// Create gRPC client
$client = new KodyEcomPaymentsServiceClient(
    $config['hostname'],
    ['credentials' => ChannelCredentials::createSsl()]
);

// Set up metadata with API key
$metadata = ['X-API-Key' => [$config['api_key']]];

// Create request
$request = new GetPaymentsRequest();
$request->setStoreId($config['store_id']);

// Set up pagination
$pageCursor = new PageCursor();
$pageCursor->setPage($currentPage);
$pageCursor->setPageSize($pageSize);
$request->setPageCursor($pageCursor);

// Make the gRPC call
try {
    list($response, $status) = $client->GetPayments($request, $metadata)->wait();

    if ($status->code !== \Grpc\STATUS_OK) {
        error_log("gRPC error: Code=" . $status->code . " Details=" . $status->details);
        die(json_encode(['error' => $status->details]));
    }

    // Check if we have a response
    if ($response->hasResponse()) {
        $responseData = $response->getResponse();
        $payments = [];
        $total = $responseData->getTotal();

        // Calculate total pages
        $totalPages = ceil($total / $pageSize);

        // Get all payments
        foreach ($responseData->getPayments() as $payment) {
            $paymentData = [
                'payment_id' => $payment->getPaymentId(),
                'payment_reference' => $payment->getPaymentReference(),
                'order_id' => $payment->getOrderId(),
                'status' => $payment->getStatus(),
                // 'psp_reference' => $payment->getPspReference(),
                // 'payment_method' => $payment->getPaymentMethod()
            ];

            // Handle optional fields
            if ($payment->hasDateCreated()) {
                $dateCreated = $payment->getDateCreated();
                $paymentData['date_created'] = date('Y-m-d H:i:s', $dateCreated->getSeconds());
            }

            if ($payment->hasDatePaid()) {
                $datePaid = $payment->getDatePaid();
                $paymentData['date_paid'] = date('Y-m-d H:i:s', $datePaid->getSeconds());
            }

            $payments[] = $paymentData;
        }

    } elseif ($response->hasError()) {
        $error = $response->getError();
        error_log("Application error: Type=" . $error->getType() . " Message=" . $error->getMessage());
        die(json_encode(['error' => $error->getMessage()]));
    }

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    die(json_encode(['error' => 'An unexpected error occurred']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Transactions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .status-SUCCESS {
            color: green;
        }
        .status-FAILED {
            color: red;
        }
        .status-PENDING {
            color: orange;
        }
        .total-count {
            margin-bottom: 20px;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination .active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
        .pagination .disabled {
            color: #aaa;
            pointer-events: none;
            border: 1px solid #ddd;
        }
        button.refund-btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            background-color: white;
            margin: 0 4px;
            cursor: pointer;
            border-radius: 4px;
        }

        button.refund-btn:hover {
            background-color: #ddd;
        }

        button.refund-btn:disabled {
            color: #aaa;
            border-color: #ddd;
            background-color: #f9f9f9;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Payment Transactions</h1>
    
    <?php if (isset($total)): ?>
    <div class="total-count">
        Total Transactions: <?php echo htmlspecialchars($total); ?>
        (Page <?php echo $currentPage + 1; ?> of <?php echo $totalPages; ?>)
    </div>
    <?php endif; ?>

    <?php if (!empty($payments)): ?>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Reference</th>
                    <th>Order ID</th>
                    <th>Status</th>
                    <!-- <th>Payment Method</th> -->
                    <th>Created Date</th>
                    <th>Paid Date</th>
                    <!-- <th>PSP Reference</th> -->
                    <th>Refund</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_reference']); ?></td>
                        <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                        <td class="status-<?php echo htmlspecialchars(getStatusText($payment['status'])); ?>">
                            <?php echo htmlspecialchars(getStatusText($payment['status'])); ?>
                        </td>
                        <!-- <td><?php echo $payment['payment_method'] ?: 'N/A'; ?></td> -->
                        <td><?php echo isset($payment['date_created']) ? htmlspecialchars($payment['date_created']) : 'N/A'; ?></td>
                        <td><?php echo isset($payment['date_paid']) ? htmlspecialchars($payment['date_paid']) : 'N/A'; ?></td>
                        <!-- <td><?php echo $payment['psp_reference'] ?: 'N/A'; ?></td> -->
                        <td>
                            <?php if (getStatusText($payment['status']) === 'SUCCESS'): ?>
                                <form action="refund-form.php" method="GET">
                                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['payment_id']); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($payment['order_id']); ?>">
                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars(getStatusText($payment['status'])); ?>">
                                    <input type="hidden" name="date_created" value="<?php echo htmlspecialchars($payment['date_created'] ?? 'N/A'); ?>">
                                    <input type="hidden" name="date_paid" value="<?php echo htmlspecialchars($payment['date_paid'] ?? 'N/A'); ?>">
                                    <button type="submit" class="refund-btn">Refund</button>
                                </form>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($currentPage > 0): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
            <?php else: ?>
                <span class="disabled">Previous</span>
            <?php endif; ?>

            <span class="active"><?php echo $currentPage + 1; ?></span>

            <?php if ($currentPage < $totalPages - 1): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
            <?php else: ?>
                <span class="disabled">Next</span>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No payments found.</p>
    <?php endif; ?>

    <script>
        function processRefund(paymentId) {
            const amountInput = document.getElementById(`refund-amount-${paymentId}`);
            const refundAmount = parseFloat(amountInput.value);

            if (isNaN(refundAmount) || refundAmount <= 0) {
                alert('Please enter a valid refund amount.');
                return;
            }

            const confirmation = confirm(`Are you sure you want to refund ${refundAmount} for Payment ID: ${paymentId}?`);
            if (!confirmation) return;

            // Call the refund API (replace with your actual API endpoint)
            fetch('refund.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_id: paymentId,
                    refund_amount: refundAmount,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Refund processed successfully.');
                        // Optionally reload or update the row
                    } else {
                        alert('Refund failed: ' + (data.error || 'Unknown error.'));
                    }
                })
                .catch(error => {
                    console.error('Error processing refund:', error);
                    alert('An error occurred while processing the refund.');
                });
        }
    </script>

</body>
</html>