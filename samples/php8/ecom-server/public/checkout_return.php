<?php

$expectedStatus = isset($_GET['status']) ? strtolower($_GET['status']) : "";
$paymentReference = isset($_GET['paymentReference']) ? $_GET['paymentReference'] : "";

if (empty($paymentReference)) {
   $message = "Missing payment reference.";
   $class = "error";
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
       .loading {
           display: flex;
           justify-content: center;
           align-items: center;
           margin: 20px;
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
       .success { background-color: #d4edda; color: #155724; }
       .failure, .failed { background-color: #f8d7da; color: #721c24; }
       .expired { background-color: #fff3cd; color: #856404; }
       .error { background-color: #f8d7da; color: #721c24; }
       .unknown { background-color: #e2e3e5; color: #383d41; }
       .invalid { background-color: #f8d7da; color: #721c24; }
       .no-result { background-color: #e2e3e5; color: #383d41; }
       .pending { background-color: #cce5ff; color: #004085; }
       .cancelled { background-color: #e2e3e5; color: #383d41; }
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
       .details-container {
           font-family: Arial, sans-serif;
           padding: 20px;
           border: 1px solid #ddd;
           margin: 20px;
           display: none;
       }
       .details-table {
           width: 100%;
           border-collapse: collapse;
       }
       .details-table td, .details-table th {
           border: 1px solid #ddd;
           padding: 8px;
           text-align: left;
       }
       .details-table tr:nth-child(even) {
           background-color: #f2f2f2;
       }
   </style>
</head>
<body>
<?php if (empty($paymentReference)): ?>
   <div class="message error">
       <?php echo htmlspecialchars($message); ?>
   </div>
<?php else: ?>
   <!-- Initial status message based on status parameter -->
   <div id="status-message" class="message <?php echo htmlspecialchars($expectedStatus); ?>">
       <?php echo htmlspecialchars(ucfirst($expectedStatus) . " payment status. Verifying..."); ?>
   </div>

   <!-- Loading indicator -->
   <div id="loading" class="loading">
       <div class="spinner"></div>
       <span>Verifying payment details...</span>
   </div>

   <!-- Payment details container (hidden initially) -->
   <div id="payment-details" class="details-container">
       <h3>Payment Details</h3>
       <table class="details-table">
           <tbody id="details-body">
               <!-- Will be populated via JavaScript -->
           </tbody>
       </table>
   </div>
<?php endif; ?>

<div class="links">
   <a href="checkout.php">New online payment</a> | <a href="index.php">Main menu</a>
</div>

<?php if (!empty($paymentReference)): ?>
<script>
   const RETRY_DELAY = 2000; // ms

   const statusMessage = document.getElementById('status-message');
   const loadingElement = document.getElementById('loading');
   const detailsContainer = document.getElementById('payment-details');
   const detailsBody = document.getElementById('details-body');

   const paymentReference = "<?php echo htmlspecialchars($paymentReference); ?>";
   const expectedStatus = "<?php echo htmlspecialchars($expectedStatus); ?>";

   function updateStatus(status, isError = false) {
       statusMessage.textContent = status;
       statusMessage.className = "message " + (isError ? "error" : status.toLowerCase());
   }

   function displayPaymentDetails(data) {
       const fields = [
           { key: 'paymentId', label: 'Payment ID' },
           { key: 'paymentReference', label: 'Payment Reference' },
           { key: 'orderId', label: 'Order ID' },
           { key: 'statusText', label: 'Status' },
           { key: 'dateCreated', label: 'Date Created' },
           { key: 'datePaid', label: 'Date Paid' }
       ];

       detailsBody.innerHTML = '';

       fields.forEach(field => {
           if (data[field.key]) {
               const row = document.createElement('tr');

               const labelCell = document.createElement('th');
               labelCell.textContent = field.label;

               const valueCell = document.createElement('td');
               valueCell.textContent = data[field.key];

               row.appendChild(labelCell);
               row.appendChild(valueCell);
               detailsBody.appendChild(row);
           }
       });

       detailsContainer.style.display = 'block';
   }

   function fetchPaymentStatus() {
       fetch(`api/payment_details.php?paymentReference=${paymentReference}`)
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   // We have valid payment details
                   loadingElement.style.display = 'none';

                   const status = data.status || 'unknown';

                   // Update status message
                   if (status === 'success') {
                       updateStatus("Payment was successful!");
                   } else {
                       updateStatus("Payment status: " + data.statusText);
                   }

                   // Optional: Validate against expected status
                   if (expectedStatus && expectedStatus !== status) {
                       updateStatus(`Warning: Expected status (${expectedStatus}) does not match actual payment status (${status}).`, true);
                   }

                   // Display payment details
                   displayPaymentDetails(data);

               } else {
                   // Always retry
                   setTimeout(fetchPaymentStatus, RETRY_DELAY);
               }
           })
           .catch(error => {
               // Always retry on network errors
               setTimeout(fetchPaymentStatus, RETRY_DELAY);
           });
   }

   fetchPaymentStatus();
</script>
<?php endif; ?>
</body>
</html>
