<?php
$config = require __DIR__ . '/config.php';
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
    <label for="amount">Amount (in minor units, e.g., 2000 for $20.00):</label>
    <input type="number" id="amount" name="amount" required>

    <label for="currency">Currency:</label>
    <input type="text" id="currency" name="currency" value="GBP" class="readonly" readonly>

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" value="your_order_id" required>

    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

    <button type="submit">Pay</button>
</form>
<?php
if (isset($_GET['error'])) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
}
?>
<h2>Developer Information</h2>
<p>This page demonstrates how to initiate a payment. The form above collects the necessary information and sends a payment request to the backend.</p>
<ul>
    <li><strong>Amount:</strong> The amount to be charged, in minor units (e.g., 2000 for £20.00).</li>
    <li><strong>Currency:</strong> The currency in which the payment will be made. This is fixed as GBP for this demo.</li>
    <li><strong>Order ID:</strong> A unique identifier for the order. This can be changed to test different orders.</li>
    <li><strong>Return URL:</strong> The URL to which the user will be redirected after the payment is completed. This is shown as a read-only field to demonstrate what the return URL will be.</li>
</ul>
</body>
</html>
