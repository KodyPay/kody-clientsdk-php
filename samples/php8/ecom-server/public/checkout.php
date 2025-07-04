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
    </style>
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

        <form action="checkout_payment.php" method="POST">
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
    </div>

    <script>
        function toggleExpirationFields() {
            const checkbox = document.getElementById('enable_expiration');
            const fields = document.getElementById('expiration_fields');

            if (checkbox && fields) {
                fields.style.display = checkbox.checked ? 'block' : 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleExpirationFields();
        });
    </script>
</body>
</html>
