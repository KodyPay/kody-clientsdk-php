<?php
$config = require __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .option {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            text-decoration: underline;
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
</div>
</body>
</html>
