<?php
$config = require __DIR__ . '/config.php';
$defaultStoreId = $_ENV['KODY_STORE_ID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 20px;
        }
        .option {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .settings {
            background-color: #e9f7ef;
            border: 1px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
        }
        .alert {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #d4edda;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            text-decoration: underline;
        }
        input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 22px);
        }
        .button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome to the Kody Store Demo</h1>
    <h3>Store ID: <?php echo htmlspecialchars($config['store_id']); ?></h3>

    <div class="option">
        <h2><a href="checkout.php">Online Payment Demo</a></h2>
        <p>Experience the online payment process.</p>
    </div>
    <div class="option">
        <h2><a href="terminals.php">List Store Payment Terminals</a></h2>
        <p>View all payment terminals assigned to the store.</p>
    </div>
    <div class="option">
        <h2><a href="transactions.php">List All Transactions</a></h2>
        <p>View all transactions made in the store.</p>
    </div>
    <div class="option settings">
        <h2>Settings - Staging Environment</h2>
        <p><b>Default Store ID:</b> <?php echo htmlspecialchars($defaultStoreId); ?></p>

        <p><b>Note:</b> You can override the default store ID and API key by entering new values below.</p>

        <div id="message" class="alert" style="display: none;"></div>

        <form id="settingsForm" onsubmit="return updateSettings(event);">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <label for="store_id" style="margin-right: 10px;">Store ID:</label>
                <input
                    type="text"
                    id="store_id"
                    name="store_id"
                    value="<?php echo isset($_COOKIE['store_id']) ? htmlspecialchars($_COOKIE['store_id']) : ''; ?>"
                    placeholder="Enter new Store ID (leave empty to use default)"
                    style="flex: 1;"
                >
            </div>
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <label for="api_key" style="margin-right: 10px;">API Key:</label>
                <input type="text" id="api_key" name="api_key"
                    value="<?php echo isset($_COOKIE['api_key']) ? htmlspecialchars($_COOKIE['api_key']) : ''; ?>"
                    placeholder="Enter new API Key (leave empty to use default)"
                    style="flex: 1;">
            </div>
            <div style="display: flex; align-items: center; justify-content: center;">
                <input type="submit" value="Save" class="button" style="margin-right: 10px;">
                <button type="button" class="button" onclick="clearSettings()">Reset</button>
            </div>
        </form>
    </div>

</div>

<script>
function updateSettings(event) {
    event.preventDefault();

    const storeId = document.getElementById('store_id').value || "<?php echo htmlspecialchars($config['store_id']); ?>";
    const apiKey = document.getElementById('api_key').value || "";
    const messageDiv = document.getElementById('message');

    if (!storeId || !apiKey) {
        messageDiv.innerHTML = "Error: Both Store ID and API Key are required!";
        messageDiv.style.display = "block";
        return false;
    }

    // Set cookies for 1 day
    document.cookie = "store_id=" + encodeURIComponent(storeId) + "; max-age=86400; path=/; SameSite=Strict";
    document.cookie = "api_key=" + encodeURIComponent(apiKey) + "; max-age=86400; path=/; SameSite=Strict";

    messageDiv.innerHTML = "Settings saved successfully!";
    messageDiv.style.display = "block";
    return false;
}

function clearSettings() {
    document.cookie = "store_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "api_key=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    document.getElementById('store_id').value = '';
    document.getElementById('api_key').value = '';

    const messageDiv = document.getElementById('message');
    messageDiv.innerHTML = "Settings cleared successfully!";
    messageDiv.style.display = "block";
}
</script>
</body>
</html>
