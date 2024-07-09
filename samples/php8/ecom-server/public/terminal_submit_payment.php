<?php

namespace KodyPayTerminalDemo;

require __DIR__ . '/config.php';

// Check if form is submitted with required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'], $_POST['tid'])) {
    $amount = (float) $_POST['amount'];
    $terminalId = $_POST['tid'];

    $client = new KodyTerminalClient();

    // Sending initial payment request
    $response = $client->sendPayment($amount, $terminalId, function ($orderId) {
        $_SESSION['current_order_id'] = $orderId;
    });

    $orderId = $_SESSION['current_order_id'] ?? null;

    if ($orderId) {
        echo "<h2>Collecting payment for Order ID: $orderId</h2>";
        echo "<div id='loading'>Loading... <img src='spinner.gif' alt='loading spinner'></div>";
        echo "<div id='payment-result' style='display:none;'></div>";
    } else {
        echo "<h2>Error: Unable to initiate payment.</h2>";
    }
} else {
    echo "<h2>Error: Invalid request.</h2>";
}
?>

<script>
    let orderId = "<?php echo $orderId; ?>";

    function checkPaymentStatus() {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'terminal_payment_status_check.php?order_id=' + orderId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.status === 'PENDING') {
                    setTimeout(checkPaymentStatus, 1000);
                } else {
                    document.getElementById('loading').style.display = 'none';
                    let resultDiv = document.getElementById('payment-result');
                    resultDiv.style.display = 'block';
                    resultDiv.innerHTML = `<h2>Payment Status: ${response.status}</h2><pre>${JSON.stringify(response, null, 2)}</pre>
                                       <a href="index.php">Start Again</a> | <a href="checkout.php">Go to Form</a>`;
                }
            } else {
                console.error('Error checking payment status:', xhr.statusText);
            }
        };
        xhr.send();
    }

    setTimeout(checkPaymentStatus, 1000);
</script>
