<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store - Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <form action="payment.php" method="POST">
        <label for="amount">Amount (in minor units, e.g., 2000 for $20.00):</label>
        <input type="number" id="amount" name="amount" required><br><br>
        <input type="hidden" name="store_id" value="your_store_id">
        <input type="hidden" name="currency" value="USD">
        <input type="hidden" name="order_id" value="your_order_id">
        <input type="hidden" name="return_url" value="http://yourdomain.com/samples/ecom/success.php">
        <button type="submit">Pay</button>
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo '<p style="color:red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>
</body>
</html>
