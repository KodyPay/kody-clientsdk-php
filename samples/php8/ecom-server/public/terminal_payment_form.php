<?php
$config = require __DIR__ . '/config.php';

// Generate a random amount between 1 and 1000
$randomAmount = rand(1, 1000);

// Generate a random order ID with 8 random letters and numbers
function generateRandomOrderId($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomOrderId = '';
    for ($i = 0; $i < $length; $i++) {
        $randomOrderId .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomOrderId;
}

$randomOrderId = generateRandomOrderId();

// Check for the tid query parameter
$terminalId = isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : '';
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
<h1>Send Payment to Terminal</h1>
<h2>Terminal ID: <?php echo $terminalId; ?></h2>
<form action="terminal_submit_payment.php" method="POST">
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" required>

    <label for="currency">Currency:</label>
    <input type="text" id="currency" name="currency" value="<?php echo $config['currency']; ?>" class="readonly" readonly>

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>

    <label for="terminal_id">Terminal ID:</label>
    <input type="text" id="terminal_id" name="terminal_id" value="<?php echo $terminalId; ?>" class="readonly" readonly>

    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

    <button type="submit">Pay</button>
</form>
<?php
if (isset($_GET['error'])) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
}
?>
<h2>Developer Information</h2>
<p>This page demonstrates how to make a payment on a terminal. The form above collects the necessary information and sends a payment request to the terminal.</p>
<ul>
    <li><strong>Amount:</strong> The amount to be charged, in minor units (e.g., 2000 for £20.00).</li>
    <li><strong>Currency:</strong> The currency in which the payment will be made. This is fixed as GBP for this demo.</li>
    <li><strong>Order ID:</strong> A unique identifier for the order. This can be changed to test different orders.</li>
    <li><strong>Terminal ID:</strong> The ID of the terminal where the payment will be processed. This is required.</li>
</ul>
</body>
</html>
