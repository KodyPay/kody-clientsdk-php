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

        select[multiple] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<h1>Send Payment to Terminal</h1>
<h2>Terminal ID: <?php echo $terminalId; ?></h2>
<form action="terminal_submit_payment.php?tid=<?php echo $terminalId; ?>" method="POST">
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" value="<?php echo $randomAmount; ?>" step="0.01" required>

    <label for="currency">Currency:</label>
    <input type="text" id="currency" name="currency" value="<?php echo $config['currency']; ?>" class="readonly" readonly>

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" value="<?php echo $randomOrderId; ?>" required>

    <label for="terminal_id">Terminal ID:</label>
    <input type="text" id="terminal_id" name="terminal_id" value="<?php echo $terminalId; ?>" class="readonly" readonly>

    <div style="display: flex; align-items: flex-end; margin-bottom: 20px;">
        <label for="show_tips">Show tips: </label>
        <input type="checkbox" id="show_tips" name="show_tips">
    </div>

    <label>Payment Method Control:</label>
    <div id="payment_method_fields" style="margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 4px">
        <label for="payment_method_type">Payment method type:</label>
        <select id="payment_method_type" name="payment_method_type">
            <option value="CARD">Card</option>
            <option value="E_WALLET">E-Wallet</option>
        </select>

        <div style="display: flex; align-items: flex-end; margin-top:10px;">
            <label for="activate_qr_code_scanner">Activate QR code scanner: </label>
            <input type="checkbox" id="activate_qr_code_scanner" name="activate_qr_code_scanner" disabled>
        </div>
    </div>

    <div id="accepts_only_section" style="margin-bottom: 20px;">
        <label>Accepts Only (press ⌘ / ctrl and click to select multiple):</label>
        <select id="accepts_only" name="accepts_only[]" multiple style="width: 100%; height: 200px;">
            <option value="VISA">Visa</option>
            <option value="MASTERCARD">Mastercard</option>
            <option value="AMEX">Amex</option>
            <option value="BAN_CONTACT">Ban Contact</option>
            <option value="CHINA_UNION_PAY">China Union Pay</option>
            <option value="MAESTRO">Maestro</option>
            <option value="DINERS">Diners</option>
            <option value="DISCOVER">Discover</option>
            <option value="JCB">JCB</option>
            <option value="ALIPAY">Alipay</option>
            <option value="WECHAT">WeChat</option>
        </select>
    </div>

    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">

    <button type="submit">Pay</button>
</form>

<script type="text/javascript">
    // Disable the QR code scanner checkbox if the payment method type is CARD
    document.getElementById('payment_method_type').addEventListener('change', function() {
        const qrScannerCheckbox = document.getElementById('activate_qr_code_scanner');
        qrScannerCheckbox.disabled = this.value !== 'E_WALLET';
        if (this.value !== 'E_WALLET') {
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
<p>This payment form accepts the following parameters:</p>
<ul>
   <li><strong>Amount:</strong> The payment amount in minor units (e.g., 2000 = £20.00)</li>
   <li><strong>Currency:</strong> Fixed as GBP for this demo (it can be configured per store)</li>
   <li><strong>Order ID:</strong> Unique order identifier, auto-generated but can be modified</li>
   <li><strong>Terminal ID:</strong> Required terminal serial number for payment processing</li>
   <li><strong>Show Tips:</strong> When enabled, displays tipping options on terminal</li>
   <li><strong>Payment Method Control:</strong>
       <ul>
           <li>Enable specific payment flows (Card or E-Wallet)</li>
           <li>QR scanner activation for E-Wallet payments (disabled if Card is selected)</li>
       </ul>
   </li>
   <li><strong>Accepts Only:</strong> Multi-select payment method filter
       <ul>
           <li>Card: Visa, Mastercard, Amex, Ban Contact, China Union Pay, Maestro, Diners, Discover, JCB</li>
           <li>E-Wallet: Alipay, WeChat</li>
           <li>If none selected, all methods are accepted</li>
       </ul>
   </li>
</ul>
</body>
</html>
