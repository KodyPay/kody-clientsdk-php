<?php
$config = require __DIR__ . '/config.php';
$defaultStoreId = $_ENV['KODY_STORE_ID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        h3 {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-weight: normal;
        }

        .option {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fafafa;
            margin-bottom: 15px;
            transition: all 0.2s ease;
            text-align: left;
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
        }

        .option:hover {
            background-color: #f0f0f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            text-decoration: none;
            color: inherit;
        }

        .option h2 {
            margin: 0 0 10px 0;
            color: #2c5aa0;
            font-size: 18px;
        }

        .option p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .settings {
            background-color: #e9f7ef;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            text-align: left;
            margin-top: 20px;
        }

        .settings h2 {
            color: #28a745;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-row label {
            min-width: 80px;
            margin-right: 15px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
            font-size: 14px;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
        }

        .button-row {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .button:hover {
            background-color: #218838;
        }

        .button.secondary {
            background-color: #6c757d;
        }

        .button.secondary:hover {
            background-color: #5a6268;
        }

        .store-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .store-info p {
            margin: 5px 0;
            color: #495057;
        }

        .section-title {
            color: #2c5aa0;
            font-size: 20px;
            margin: 30px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }

        .section-title:first-of-type {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Kody Store Demo</h1>
        <h3>Store ID: <span id="storeIdDisplay"><?php echo htmlspecialchars($config['store_id']); ?></span></h3>

        <!-- Online Payments -->
        <h2 class="section-title">Online Payments</h2>
        <a href="checkout.php" class="option">
            <h2>Online Payment Demo</h2>
            <p>Experience the online payment process</p>
        </a>

        <!-- Terminal Payments -->
        <h2 class="section-title">Terminal Payments</h2>
        <a href="terminals.php" class="option">
            <h2>Terminals</h2>
            <p>View all terminals assigned to the store</p>
        </a>

        <!-- Token Management -->
        <h2 class="section-title">Token Management</h2>

        <a href="token-payment.php" class="option">
            <h2>Token Payment</h2>
            <p>Process payments using saved payment tokens</p>
        </a>

        <a href="token-payment-tokens.php" class="option">
            <h2>Card Tokens</h2>
            <p>View all saved card tokens in the store</p>
        </a>

        <!-- History & Logs -->
        <h2 class="section-title">History & Logs</h2>
        <a href="transactions.php" class="option">
            <h2>All Transactions</h2>
            <p>View all transactions made in the store</p>
        </a>

        <a href="logs.php" class="option">
            <h2>View Logs</h2>
            <p>View all logs made in the store</p>
        </a>

        <div class="settings">
            <h2>Settings - Staging Environment</h2>

            <div class="store-info">
                <p><strong>Default Store ID:</strong> <?php echo htmlspecialchars($defaultStoreId); ?></p>
                <p><strong>Note:</strong> You can override the default store ID and API key by entering new values below.</p>
            </div>

            <div id="message" class="alert" style="display: none;"></div>

            <form id="settingsForm" onsubmit="return updateSettings(event);">
                <div class="form-row">
                    <label for="store_id">Store ID:</label>
                    <input
                        type="text"
                        id="store_id"
                        name="store_id"
                        value="<?php echo isset($_COOKIE['store_id']) ? htmlspecialchars($_COOKIE['store_id']) : ''; ?>"
                        placeholder="Enter new Store ID (leave empty to use default)"
                    >
                </div>

                <div class="form-row">
                    <label for="api_key">API Key:</label>
                    <input
                        type="text"
                        id="api_key"
                        name="api_key"
                        value="<?php echo isset($_COOKIE['api_key']) ? htmlspecialchars($_COOKIE['api_key']) : ''; ?>"
                        placeholder="Enter new API Key (leave empty to use default)"
                    >
                </div>

                <div class="button-row">
                    <input type="submit" value="Save" class="button">
                    <button type="button" class="button secondary" onclick="clearSettings()">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateSettings(event) {
            event.preventDefault();

            const storeId = document.getElementById('store_id').value;
            const apiKey = document.getElementById('api_key').value;
            const messageDiv = document.getElementById('message');
            const storeIdDisplay = document.getElementById('storeIdDisplay');

            if (!storeId || !apiKey) {
                messageDiv.innerHTML = "Error: Both Store ID and API Key are required!";
                messageDiv.style.display = "block";
                messageDiv.style.backgroundColor = "#f8d7da";
                messageDiv.style.borderColor = "#f5c6cb";
                messageDiv.style.color = "#721c24";
                return false;
            }

            // Set cookies for 1 day
            document.cookie = "store_id=" + encodeURIComponent(storeId) + "; max-age=86400; path=/; SameSite=Strict";
            document.cookie = "api_key=" + encodeURIComponent(apiKey) + "; max-age=86400; path=/; SameSite=Strict";

            // Update Store ID display
            storeIdDisplay.textContent = storeId;

            messageDiv.innerHTML = "Settings saved successfully!";
            messageDiv.style.display = "block";
            messageDiv.style.backgroundColor = "#d4edda";
            messageDiv.style.borderColor = "#c3e6cb";
            messageDiv.style.color = "#155724";
            return false;
        }

        function clearSettings() {
            document.cookie = "store_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "api_key=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

            document.getElementById('store_id').value = '';
            document.getElementById('api_key').value = '';

            // Reset Store ID display
            const storeIdDisplay = document.getElementById('storeIdDisplay');
            storeIdDisplay.textContent = "<?php echo htmlspecialchars($defaultStoreId); ?>";

            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = "Settings cleared successfully!";
            messageDiv.style.display = "block";
            messageDiv.style.backgroundColor = "#d4edda";
            messageDiv.style.borderColor = "#c3e6cb";
            messageDiv.style.color = "#155724";
        }
    </script>
</body>
</html>
