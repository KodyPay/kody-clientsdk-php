<?php
// Get URL parameters
$token_id = isset($_GET['token_id']) ? htmlspecialchars($_GET['token_id']) : null;
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

// Determine if the token creation was successful
$isSuccess = $status === 'success' || ($token_id && !$error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Creation Result - Online Store</title>
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

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .success h1 {
            color: #28a745;
        }

        .error h1 {
            color: #dc3545;
        }

        .success-icon {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
            text-align: center;
        }

        .token-info {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .token-info h3 {
            margin-top: 0;
            color: #333;
        }

        .token-info p {
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .next-steps {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: left;
        }

        .next-steps h3 {
            margin-top: 0;
            color: #0c5460;
        }

        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .next-steps li {
            margin: 5px 0;
        }

        .error-details {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: left;
        }

        .error-details h3 {
            margin-top: 0;
            color: #721c24;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <?php if ($isSuccess): ?>
            <!-- Success State -->
            <div class="success">
                <div class="success-icon">✅</div>
                <h1>Token Created Successfully!</h1>
                <div class="message">
                    Your payment token has been created and stored securely. You can now use this token for future payments without entering your card details again.
                </div>

                <?php if ($token_id): ?>
                <div class="token-info">
                    <h3>Token Information</h3>
                    <p><strong>Token ID:</strong> <?php echo $token_id; ?></p>
                    <p><strong>Created:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    <p><strong>Status:</strong> Active</p>
                </div>
                <?php endif; ?>

                <div class="next-steps">
                    <h3>What's Next?</h3>
                    <ul>
                        <li>Your payment method is now securely stored</li>
                        <li>You can use this token for future purchases</li>
                        <li>No need to enter card details again</li>
                        <li>You can manage your payment methods in your account</li>
                    </ul>
                </div>

                <div class="links">
                    <a href="/index.php">Main menu</a>
                    <a href="/token-payment.php">Create Another Token</a>
                    <a href="/token-payment-tokens.php">View My Tokens</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Error State -->
            <div class="error">
                <div class="error-icon">❌</div>
                <h1>Token Creation Failed</h1>
                <div class="message">
                    Unfortunately, we were unable to create your payment token. Please try again or contact support if the problem persists.
                </div>

                <?php if ($error): ?>
                <div class="error-details">
                    <h3>Error Details</h3>
                    <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <div class="next-steps">
                    <h3>What You Can Do</h3>
                    <ul>
                        <li>Try creating the token again</li>
                        <li>Check that your card details were entered correctly</li>
                        <li>Ensure your internet connection is stable</li>
                        <li>Contact our support team if the issue continues</li>
                    </ul>
                </div>

                <div class="links">
                    <a href="/index.php">Main menu</a>
                    <a href="/token-payment.php">Try Again</a>
                    <a href="/token-payment-tokens.php">View My Tokens</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
