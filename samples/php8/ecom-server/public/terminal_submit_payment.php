<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PaymentStatus;
use Com\Kodypay\Grpc\Pay\V1\PayRequest;
use Grpc\ChannelCredentials;
use Grpc\Metadata;

error_log("Processing terminal payment");

// Check if form is submitted with required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['amount'], $_POST['order_id'], $_POST['terminal_id'], $_POST['currency'])) {

    $amount = (float)$_POST['amount'];

    $client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    // Sending initial payment request
    $req = new PayRequest();
    $req->setStoreId($config['store_id']);
    $req->setAmount(number_format($amount, 2, '.', ''));
    $req->setTerminalId($_POST['terminal_id']);

    error_log("Sending request");
    $timeoutDateTime = (new DateTime())->add(new DateInterval('PT' . (3 * 60) . 'S'));
    $call = $client->Pay($req, $metadata, ['timeout' => $timeoutDateTime]);

    error_log("Request submitted");

    // Capture the orderId from the callback
    $orderId = null;
    foreach ($call->responses() as $reply) {
        if ($reply->getStatus() === PaymentStatus::PENDING) {
            $orderId = $reply->getOrderId();
            $_SESSION['current_order_id'] = $orderId;
            break;
        }
    }

    if ($orderId) {
        echo "<h2>Collecting payment for Order ID: $orderId</h2>";
        echo "<div id='loading'>Loading... <img src='spinner.gif' alt='loading spinner'></div>";
        echo "<div id='payment-result' style='display:none;'></div>";
    } else {
        error_log("Error: Unable to initiate payment.");
        echo "<h2>Error: Unable to initiate payment.</h2>";
    }
} else {
    error_log("Error: Invalid request. Form parameters are missing.");
    echo "<h2>Error: Invalid request.</h2>";
}
?>

<?php if (isset($orderId)): ?>
    <script>
        let orderId = "<?php echo $orderId; ?>";

        function checkPaymentStatus() {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', 'terminal_payment_status_check.php?order_id=' + orderId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === 0) {
                        setTimeout(checkPaymentStatus, 1000);
                    } else {
                        document.getElementById('loading').style.display = 'none';
                        let resultDiv = document.getElementById('payment-result');
                        resultDiv.style.display = 'block';
                        resultDiv.innerHTML = `<h2>Payment Status: ${response.status}</h2><pre>${JSON.stringify(response, null, 2)}</pre>
                                       <a href="terminal_payment_form.php">New payment</a> | <a href="terminals.php">Terminals list</a>`;
                    }
                } else {
                    console.error('Error checking payment status:', xhr.statusText);
                }
            };
            xhr.send();
        }

        setTimeout(checkPaymentStatus, 1000);
    </script>
<?php endif; ?>
