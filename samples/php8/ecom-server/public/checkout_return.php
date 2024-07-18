<?php
// payment_result.php

// Check if the 'result' query parameter is set
if (isset($_GET['result'])) {
    // Get the value of the 'result' query parameter
    $result = $_GET['result'];

    // Define the possible payment outcomes
    $paymentOutcomes = ['success', 'failure', 'expired', 'error'];

    // Check if the result is a valid payment outcome
    if (in_array($result, $paymentOutcomes)) {
        switch ($result) {
            case 'success':
                $message = "Payment was successful!";
                $class = "success";
                break;
            case 'failure':
                $message = "Payment failed. Please try again.";
                $class = "failure";
                break;
            case 'expired':
                $message = "Payment session has expired. Please start again.";
                $class = "expired";
                break;
            case 'error':
                $message = "An error occurred during payment. Please contact support.";
                $class = "error";
                break;
            default:
                $message = "Unknown payment result.";
                $class = "unknown";
                break;
        }
    } else {
        $message = "Invalid payment result.";
        $class = "invalid";
    }
} else {
    $message = "No payment result provided.";
    $class = "no-result";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <style>
        .message {
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px;
            text-align: center;
        }
        .success { background-color: #d4edda; color: #155724; }
        .failure { background-color: #f8d7da; color: #721c24; }
        .expired { background-color: #fff3cd; color: #856404; }
        .error { background-color: #f8d7da; color: #721c24; }
        .unknown { background-color: #e2e3e5; color: #383d41; }
        .invalid { background-color: #f8d7da; color: #721c24; }
        .no-result { background-color: #e2e3e5; color: #383d41; }
        .links {
            text-align: center;
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        .links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="message <?php echo $class; ?>">
    <?php echo $message; ?>
</div>
<div class="links">
    <a href="checkout.php">New online payment</a> | <a href="index.php">Main menu</a>
</div>
</body>
</html>
