<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PaymentMethod;
use Com\Kodypay\Grpc\Pay\V1\PaymentMethodType;
use Com\Kodypay\Grpc\Pay\V1\PaymentStatus;
use Com\Kodypay\Grpc\Pay\V1\PayRequest;
use Com\Kodypay\Grpc\Pay\V1\PayRequest\PaymentMethods;
use Grpc\ChannelCredentials;
use Grpc\Metadata;

error_log("Processing terminal payment");

// Check if form is submitted with required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['amount'], $_POST['order_id'], $_POST['terminal_id'], $_POST['currency'])) {

    $amount = (float)$_POST['amount'];

    $client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
    $metadata = ['X-API-Key' => [$config['api_key']]];

    error_log("Requesting amount: $amount");

    // Sending initial payment request
    $req = new PayRequest();
    $req->setStoreId($config['store_id']);
    $req->setAmount(number_format($amount, 2, '.', ''));
    $req->setTerminalId($_POST['terminal_id']);
    $req->setShowTips((isset($_POST['show_tips'])) ? $_POST['show_tips'] : false);

    if (isset($_POST['payment_method_type']) or isset($_POST['activate_qr_code_scanner'])) {
        $paymentMethod = new PaymentMethod();

        if (isset($_POST['payment_method_type'])) {
            $paymentMethod->setPaymentMethodType(PaymentMethodType::value($_POST['payment_method_type']));
        }

        if (isset($_POST['activate_qr_code_scanner'])) {
            $paymentMethod->setActivateQrCodeScanner($_POST['activate_qr_code_scanner']);
        }

        $req->setPaymentMethod($paymentMethod);
    }

    if (isset($_POST['accepts_only']) && is_array($_POST['accepts_only'])) {
        $acceptsOnly = array_map(function($method) {
            return constant(PaymentMethods::class . '::' . $method);
        }, $_POST['accepts_only']);
        $req->setAcceptsOnly($acceptsOnly);
    }

    error_log("Sending request");
    $timeoutDateTime = (new DateTime())->add(new DateInterval('PT' . (3 * 60) . 'S'));

    $call = $client->Pay($req, $metadata, ['timeout' => $timeoutDateTime]);

    error_log("Request submitted");

    // Capture the paymentId from the callback
    $paymentId = null;
    foreach ($call->responses() as $reply) {
        if ($reply->getStatus() === PaymentStatus::PENDING) {
            $paymentId = $reply->getPaymentId();
            $_SESSION['current_payment_id'] = $paymentId;
            break;
        }
    }

    if ($paymentId) {
        echo "<h2>Collecting payment for Payment ID: $paymentId</h2>";
        echo "<div id='loading'>Waiting for payment...</div>";
        echo "<div id='payment-result' style='display:none;'></div>";
        echo "<div id='cancel-button' style='display:none;'>";
        echo "<form action='terminal_cancel_payment.php' method='POST'>";
        echo "<input type='hidden' name='terminal_id' value='" . htmlspecialchars($_POST['terminal_id']) . "'>";
        echo "<input type='hidden' name='store_id' value='" . htmlspecialchars($config['store_id']) . "'>";
        echo "<input type='hidden' name='amount' value='" . htmlspecialchars($amount) . "'>";
        echo "<input type='hidden' name='payment_id' value='" . htmlspecialchars($paymentId) . "'>";
        echo "<button type='submit'>Cancel Payment</button>";
        echo "</form>";
        echo "</div>";
    } else {
        error_log("Error: Unable to initiate payment.");
        echo "<h2>Error: Unable to initiate payment.</h2>";
    }
} else {
    error_log("Error: Invalid request. Form parameters are missing.");
    echo "<h2>Error: Invalid request.</h2>";
}
?>

<?php if (isset($paymentId)): ?>
    <script>
        let paymentId = "<?php echo $paymentId; ?>";
        let maxRetries = 60;
        let retryCount = 0;
        let interval = 1000; // 1 second

        function checkPaymentStatus() {
            fetch('terminal_payment_status_check.php?payment_id=' + paymentId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(response => {
                if (response.status === 0) {
                    // Payment still pending, show cancel button and continue polling
                    document.getElementById('cancel-button').style.display = 'block';
                    scheduleNextCheck();
                } else {
                    // Payment completed with success or error
                    document.getElementById('loading').style.display = 'none';
                    let resultDiv = document.getElementById('payment-result');
                    resultDiv.style.display = 'block';
                    resultDiv.innerHTML = `<h2>Payment Status: ${response.status}</h2><pre>${JSON.stringify(response, null, 2)}</pre>
                                       <a href="terminal_payment_form.php?tid=<?php echo htmlspecialchars($_POST['terminal_id'] ?? ''); ?>">New payment</a> | <a href="terminals.php">Terminals list</a>`;
                    document.getElementById('cancel-button').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
                scheduleNextCheck();
            });
        }

        function scheduleNextCheck() {
            retryCount++;
            if (retryCount <= maxRetries) {
                setTimeout(checkPaymentStatus, interval);
            } else {
                document.getElementById('loading').style.display = 'none';
                let resultDiv = document.getElementById('payment-result');
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = `<h2>Payment Status Check Timed Out</h2>
                                      <p>The payment status check has timed out after ${maxRetries} attempts.</p>
                                      <a href="terminal_payment_form.php?tid=<?php echo htmlspecialchars($_POST['terminal_id'] ?? ''); ?>">New payment</a> | <a href="terminals.php">Terminals list</a>`;
            }
        }

        // Start checking payment status
        setTimeout(checkPaymentStatus, interval);
    </script>
    <script src="js/bubble.php"></script>
<?php endif; ?>
