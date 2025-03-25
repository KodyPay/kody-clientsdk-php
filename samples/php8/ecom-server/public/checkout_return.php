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
        .message {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px;
            text-align: center;
        }
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
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
        .success { background-color: #d4edda; color: #155724; }
        .failure, .failed { background-color: #f8d7da; color: #721c24; }
        .expired { background-color: #fff3cd; color: #856404; }
        .error { background-color: #f8d7da; color: #721c24; }
        .unknown { background-color: #e2e3e5; color: #383d41; }
        .invalid { background-color: #f8d7da; color: #721c24; }
        .no-result { background-color: #e2e3e5; color: #383d41; }
        .pending { background-color: #cce5ff; color: #004085; }
        .cancelled { background-color: #e2e3e5; color: #383d41; }
        .links {
            text-align: center;
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        .links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
        }
        .details-container {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px;
            display: none;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td, .details-table th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php if (empty($paymentReference)): ?>
    <div class="message error">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php else: ?>
    <!-- Initial status message based on status parameter -->
    <div id="status-message" class="message <?php echo htmlspecialchars($expectedStatus); ?>">
        <?php echo htmlspecialchars(ucfirst($expectedStatus) . " payment status."); ?>
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
    <a href="checkout.php">New online payment</a> | <a href="index.php">Main menu</a>
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

    function updateStatus(status, isError = false) {
        statusMessage.textContent = status;
        statusMessage.className = "message " + (isError ? "error" : status.toLowerCase());
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = data.status || 'unknown';
                    const statusText = data.statusText || '';

                    // If payment status is pending, treat it as success but continue retrying
                    if (status === 'pending') {
                        updateStatus("Payment successful, awaiting confirmation.");
                        displayPaymentDetails(data);

                        if (retryCount < MAX_RETRIES) {
                            retryCount++;
                            setTimeout(fetchPaymentStatus, RETRY_DELAY);
                        } else {
                            loadingElement.style.display = 'none';
                            updateStatus("Payment successful, awaiting confirmation. Please check back later.");
                        }
                    } else if (status === 'success') {
                        // Success status received
                        loadingElement.style.display = 'none';
                        updateStatus("Payment was successful!");
                        displayPaymentDetails(data);
                    } else {
                        // Other status (failure, expired, etc.)
                        loadingElement.style.display = 'none';
                        updateStatus("Payment status: " + statusText);
                        displayPaymentDetails(data);
                    }

                    // Validate against expected status - treat "error" and "failed" as equivalent
                    if (expectedStatus && expectedStatus !== status && status !== 'pending') {
                        // If both are error types, don't show a warning
                        const errorTypes = ['error', 'failed', 'failure'];
                        if (!(errorTypes.includes(expectedStatus) && errorTypes.includes(status))) {
                            updateStatus(`Warning: Expected status (${expectedStatus}) does not match actual payment status (${status}).`, true);
                        }
                    }
                } else {
                    if (retryCount < MAX_RETRIES) {
                        retryCount++;
                        setTimeout(fetchPaymentStatus, RETRY_DELAY);
                    } else {
                        loadingElement.style.display = 'none';
                        updateStatus("Payment details not found after multiple attempts.", true);
                    }
                }
            })
            .catch(error => {
                if (retryCount < MAX_RETRIES) {
                    retryCount++;
                    setTimeout(fetchPaymentStatus, RETRY_DELAY);
                } else {
                    loadingElement.style.display = 'none';
                    updateStatus("Failed to verify payment after multiple attempts.", true);
                }
            });
    }

    fetchPaymentStatus();
</script>
<?php endif; ?>
</body>
</html>
