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
        input[type="url"],
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .readonly {
            background-color: #f9f9f9;
            color: #666;
        }

        .payment-section {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
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

        select[multiple] {
            height: 200px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .help-text {
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
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
    </style>
    <script src="js/bubble.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/terminals.php">← Back to Terminals</a>
        </div>

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

            <div class="checkbox-container">
                <input type="checkbox" id="show_tips" name="show_tips">
                <label for="show_tips">Show tips</label>
            </div>

            <div class="payment-section">
                <div class="section-title">Payment Method Control</div>

                <label for="payment_method_type">Payment method type:</label>
                <select id="payment_method_type" name="payment_method_type">
                    <option value="CARD">Card</option>
                    <option value="E_WALLET">E-Wallet</option>
                </select>

                <div class="checkbox-container">
                    <input type="checkbox" id="activate_qr_code_scanner" name="activate_qr_code_scanner" disabled>
                    <label for="activate_qr_code_scanner">Activate QR code scanner</label>
                </div>
            </div>

            <div class="payment-section">
                <div class="section-title">Accepts Only</div>
                <div class="help-text">Press ⌘ / Ctrl and click to select multiple payment methods</div>

                <select id="accepts_only" name="accepts_only[]" multiple>
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

        <div class="links">
            <a href="/terminals.php">Back to terminal list</a> | <a href="/index.php">Main menu</a>
        </div>
    </div>

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
        echo '<div class="error-message">Error: ' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <div class="dev-info">
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
    </div>
</body>
</html>
