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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store - Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="number"],
        input[type="text"],
        input[type="url"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .readonly {
            background-color: #f9f9f9;
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
<h1>Checkout</h1>
<form action="checkout_payment.php" method="POST">
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" required min="1" step="1" required>

    <label for="currency">Currency:</label>
    <input type="text" id="currency" name="currency" value="<?php echo $config['currency']; ?>" class="readonly" readonly>

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>

    <div style="display: flex; align-items: flex-end; margin-bottom: 20px;">
        <label for="enable_expiration">Enable expiration: </label>
        <input type="checkbox" id="enable_expiration" name="enable_expiration" onchange="valueChanged()">
    </div>

    <div id="expiration_fields" style="display: none; margin-bottom: 20px;
    padding: 20px; border: 1px solid #ccc; border-radius: 4px">
        <label for="expiring_seconds">Expiring seconds:</label>
        <input type="number" id="expiring_seconds" name="expiring_seconds" min="0" oninput="validity.valid||(value='');"
               value="<?php echo $config['expiring_seconds']; ?>">

        <label for="show_timer">Show timer:</label>
        <div style="margin-top:5px;">
            <select id="show_timer" name="show_timer">
                <option value="true">true</option>
                <option value="">false</option>
            </select>
        </div>
    </div>

    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

    <button type="submit">Pay</button>
</form>

<script type="text/javascript">
    function valueChanged() {
        if (document.getElementById('enable_expiration').checked) {
            document.getElementById("expiration_fields").style.display = 'block';
        } else {
            document.getElementById("expiration_fields").style.display = 'none';
        }
    }
</script>

<?php
if (isset($_GET['error'])) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<h2>Developer Information</h2>
<p>This page demonstrates how to initiate a payment using the KodyEcomPaymentsService API. The form above collects the necessary information and sends a payment request to the backend.</p>
<ul>
    <li><strong>Amount:</strong> The amount to be charged in minor units (e.g., 2000 for $20.00). This corresponds to the <code>amount_minor_units</code> field in the API.</li>
    <li><strong>Currency:</strong> The ISO 4217 three-letter currency code (e.g., GBP, USD) in which the payment will be made.</li>
    <li><strong>Order ID:</strong> Your unique identifier for this order. This can be reused if the same order has multiple payments.</li>
    <li><strong>Store ID:</strong> Your Kody store identifier (hidden field). This is required for all API calls.</li>
    <li><strong>Enable expiration:</strong> Configure payment expiration settings.</li>
    <li style="list-style-type: none">
        <ul class="inside">
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

<p>For more detailed information about the API, please refer to the <a href="https://api-docs.kody.com/docs/payments-api/ecom-payments#1-initiate-payment" target="_blank">Kody Payments API Documentation</a>.</p>
</body>
</html>
