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

$paymentId = null;
$errorMessage = null;

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

    if (!$paymentId) {
        error_log("Error: Unable to initiate payment.");
        $errorMessage = "Error: Unable to initiate payment.";
    }
} else {
    error_log("Error: Invalid request. Form parameters are missing.");
    $errorMessage = "Error: Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Payment Processing</title>
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

        h1, h2 {
            color: #333;
            margin-bottom: 20px;
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

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        button {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #c82333;
        }

        .error-message {
            color: red;
            padding: 20px;
            border: 1px solid #ffcccc;
            background-color: #fff8f8;
            margin: 20px 0;
            border-radius: 6px;
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

        #payment-result {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        #cancel-button {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/terminal_payment_form.php<?php echo isset($_POST['terminal_id']) ? '?tid=' . urlencode($_POST['terminal_id']) : ''; ?>">← Back to Payment Form</a>
        </div>

        <h1>Terminal Payment Processing</h1>

        <?php if ($errorMessage): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php elseif ($paymentId): ?>
            <h2>Collecting payment for Payment ID: <?php echo htmlspecialchars($paymentId); ?></h2>

            <div id="loading" class="loading">
                <div class="spinner"></div>
                <span>Waiting for payment...</span>
            </div>

            <div id="payment-result" style="display:none;"></div>

            <div id="cancel-button" style="display:none;">
                <form action="terminal_cancel_payment.php" method="POST">
                    <input type="hidden" name="terminal_id" value="<?php echo htmlspecialchars($_POST['terminal_id']); ?>">
                    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($config['store_id']); ?>">
                    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($paymentId); ?>">
                    <button type="submit">Cancel Payment</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

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
</body>
</html>
