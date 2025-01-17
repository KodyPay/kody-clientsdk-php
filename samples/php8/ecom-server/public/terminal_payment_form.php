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
    <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" step="0.01" required>

    <label for="currency">Currency:</label>
    <input type="text" id="currency" name="currency" value="<?php echo $config['currency']; ?>" class="readonly"
           readonly>

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>

    <label for="terminal_id">Terminal ID:</label>
    <input type="text" id="terminal_id" name="terminal_id" value="<?php echo $terminalId; ?>" class="readonly" readonly>

    <div style="display: flex; align-items: flex-end; margin-bottom: 20px;">
        <label for="show_tips">Show tips: </label>
        <input type="checkbox" id="show_tips" name="show_tips">
    </div>

    <div style="display: flex; align-items: flex-end; margin-bottom: 20px;">
        <label for="enable_payment_method">Enable payment method: </label>
        <input type="checkbox" id="enable_payment_method" name="enable_payment_method" onchange="valueChanged()">
    </div>

    <div id="payment_method_fields" style="display: none; margin-bottom: 20px;
    padding: 20px; border: 1px solid #ccc; border-radius: 4px">
        <label for="payment_method_type">Payment method type:</label>
        <select id="payment_method_type" name="payment_method_type">
            <option value="CARD">Card</option>
            <option value="ALIPAY">AliPay+</option>
            <option value="WECHAT">WeChat</option>
        </select>

        <div style="display: flex; align-items: flex-end; margin-top:10px;">
            <label for="activate_qr_code_scanner">Activate QR code scanner: </label>
            <input type="checkbox" id="activate_qr_code_scanner" name="activate_qr_code_scanner" disabled>
        </div>
    </div>

    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

    <button type="submit">Pay</button>
</form>

<script type="text/javascript">
    function valueChanged() {
        if (document.getElementById('enable_payment_method').checked) {
            document.getElementById("payment_method_fields").style.display = 'block';
        } else {
            document.getElementById("payment_method_fields").style.display = 'none';
        }
    }

    // Disable the QR code scanner checkbox if the payment method type is CARD
    document.getElementById('payment_method_type').addEventListener('change', function() {
        const qrScannerCheckbox = document.getElementById('activate_qr_code_scanner');
        qrScannerCheckbox.disabled = this.value === 'CARD';
        if (this.value === 'CARD') {
            qrScannerCheckbox.checked = false;
        }
    });
</script>

<?php
if (isset($_GET['error'])) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
}
?>
<h2>Developer Information</h2>
<p>This page demonstrates how to make a payment on a terminal. The form above collects the necessary information and
    sends a payment request to the terminal.</p>
<ul>
    <li><strong>Amount:</strong> The amount to be charged, in minor units (e.g., 2000 for £20.00).</li>
    <li><strong>Currency:</strong> The currency in which the payment will be made. This is fixed as GBP for this demo.
    </li>
    <li><strong>Order ID:</strong> A unique identifier for the order. This can be changed to test different orders.</li>
    <li><strong>Terminal ID:</strong> The ID of the terminal where the payment will be processed. This is required.</li>
    <li><strong>Show tips:</strong> A flag to show (true) or hide (false) the tip options. Default is (false). This is
        optional.
    </li>
    <li><strong>Enable payment method:</strong> Show the payment method options and include them in the request.</li>
    <li style="list-style-type: none">
        <ul class="inside">
            <li><strong>Payment method type:</strong> Payment method type: CARD (default), ALIPAY, WECHAT.</li>
            <li><strong>Activate QR code scanner:</strong> Flag to activate the terminal camera to scan the customer's
                QR Code (true), or display the payment method type QR Code for the user to scan (false, default).
            </li>
        </ul>
    </li>
</ul>
</body>
</html>
