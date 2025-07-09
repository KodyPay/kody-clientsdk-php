<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Tokens</title>
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

        h1 {
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

        .payer-input {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .payer-input label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
        }

        .payer-input input {
            width: 300px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }

        .payer-input button {
            margin-left: 10px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .payer-input button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #fafafa;
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 40px 0;
            flex-direction: column;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: red;
            padding: 20px;
            border: 1px solid #ffcccc;
            background-color: #fff8f8;
            margin: 20px 0;
            border-radius: 6px;
        }

        .no-tokens {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }

        .status-ready {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }

        .status-failed {
            color: #e74c3c;
            font-weight: bold;
        }

        .status-deleted {
            color: #9e9e9e;
            font-weight: bold;
        }

        .payment-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .payment-btn:hover {
            background-color: #45a049;
        }

        .payment-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .delete-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .button-group {
            display: flex;
            gap: 5px;
            align-items: center;
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

        .card-info {
            font-size: 12px;
            color: #666;
        }

        .token-reference {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }
    </style>
    <script src="js/bubble.php"></script>
    <script>
        let currentPayerReference = '';

        function fetchTokens() {
            const payerReference = document.getElementById('payer-reference').value.trim();

            if (!payerReference) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Please enter a payer reference to view tokens</div>';
                return;
            }

            if (payerReference === currentPayerReference) {
                // Don't reload if same payer reference
                return;
            }

            currentPayerReference = payerReference;
            const container = document.getElementById('tokens-container');

            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Loading tokens...</span>
                </div>`;

            fetch('api/get-card-tokens.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payer_reference: payerReference
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to fetch tokens');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to fetch tokens');
                    }

                    if (!data.tokens || data.tokens.length === 0) {
                        container.innerHTML = '<div class="no-tokens">No tokens found for this payer reference</div>';
                        return;
                    }

                    // Build tokens table
                    let html = `
                        <table id="tokens-table">
                            <thead>
                                <tr>
                                    <th>Token ID</th>
                                    <th>Payment Token</th>
                                    <th>Card Info</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Token Reference</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.tokens.forEach(token => {
                        const status = getStatusText(token.status);
                        const statusClass = getStatusClass(token.status);
                        const createdDate = token.created_at ? new Date(token.created_at).toLocaleString() : 'N/A';
                        const cardInfo = `${getPaymentMethodText(token.payment_method)} **** ${token.card_last_4_digits}`;
                        const tokenReference = token.token_reference || 'N/A';

                        // Show appropriate buttons based on token status
                        let actionButtons = '<div class="button-group">';
                        
                        // Payment button - only available for READY tokens
                        if (token.status === 2) { // READY status
                            actionButtons += `<button onclick="useTokenForPayment('${token.payment_token}')" class="payment-btn">Use for Payment</button>`;
                        } else {
                            actionButtons += '<span class="card-info">Not available</span>';
                        }
                        
                        // Delete button - available for READY, PENDING, and FAILED tokens (not DELETED or PENDING_DELETE)
                        if (token.status !== 3 && token.status !== 4) { // Not DELETED or PENDING_DELETE
                            actionButtons += `<button onclick="deleteToken('${token.token_id}')" class="delete-btn">Delete</button>`;
                        }
                        
                        actionButtons += '</div>';
                        const actionButton = actionButtons;

                        html += `
                            <tr>
                                <td>${token.token_id}</td>
                                <td class="card-info">${token.payment_token}</td>
                                <td>${cardInfo}</td>
                                <td class="${statusClass}">${status}</td>
                                <td class="card-info">${createdDate}</td>
                                <td class="token-reference">${tokenReference}</td>
                                <td>${actionButton}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching tokens:', error);
                    container.innerHTML = `<div class="error-message">${escapeHtml(error.message)}</div>`;
                });
        }

        function getStatusText(status) {
            switch(status) {
                case 0: return 'Pending';
                case 1: return 'Failed';
                case 2: return 'Ready';
                case 3: return 'Deleted';
                case 4: return 'Pending Delete';
                default: return 'Unknown';
            }
        }

        function getStatusClass(status) {
            switch(status) {
                case 0: return 'status-pending';
                case 1: return 'status-failed';
                case 2: return 'status-ready';
                case 3: return 'status-deleted';
                case 4: return 'status-deleted';
                default: return '';
            }
        }

        function getPaymentMethodText(method) {
            switch(method) {
                case 0: return 'Visa';
                case 1: return 'Mastercard';
                case 2: return 'Amex';
                case 3: return 'Bancontact';
                case 4: return 'China UnionPay';
                case 5: return 'Maestro';
                case 6: return 'Diners';
                case 7: return 'Discover';
                case 8: return 'JCB';
                case 9: return 'Alipay';
                case 10: return 'WeChat';
                default: return 'Unknown';
            }
        }

        function useTokenForPayment(paymentToken) {
            // Redirect to token payment page with the selected token
            window.location.href = `token-payment-tokens.php?payment_token=${encodeURIComponent(paymentToken)}`;
        }

        function deleteToken(tokenId) {
            if (!confirm('Are you sure you want to delete this token? This action cannot be undone.')) {
                return;
            }

            // Disable the delete button to prevent multiple clicks
            const deleteButtons = document.querySelectorAll(`button[onclick="deleteToken('${tokenId}')"]`);
            deleteButtons.forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Deleting...';
            });

            fetch('api/delete-card-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token_id: tokenId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Failed to delete token');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to delete token');
                    }

                    // Success - refresh the tokens list
                    alert('Token deleted successfully');
                    fetchTokens();
                })
                .catch(error => {
                    console.error('Error deleting token:', error);
                    alert('Error deleting token: ' + error.message);
                    
                    // Re-enable the button on error
                    deleteButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.textContent = 'Delete';
                    });
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function onPayerReferenceChange() {
            // Clear current tokens when payer reference changes
            if (currentPayerReference !== document.getElementById('payer-reference').value.trim()) {
                document.getElementById('tokens-container').innerHTML = '<div class="no-tokens">Click "Load Tokens" or press Enter to view tokens for this payer</div>';
                currentPayerReference = '';
            }
        }

        function handleKeyPress(event) {
            // Check if Enter key was pressed
            if (event.key === 'Enter' || event.keyCode === 13) {
                event.preventDefault(); // Prevent form submission if inside a form
                fetchTokens();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Card Tokens</h1>

        <div class="payer-input">
            <label for="payer-reference">Payer Reference:</label>
            <input type="text" id="payer-reference" placeholder="Enter payer reference (e.g., user123)" onchange="onPayerReferenceChange()" onkeypress="handleKeyPress(event)">
            <button onclick="fetchTokens()">Load Tokens</button>
        </div>

        <div id="tokens-container">
            <div class="no-tokens">Please enter a payer reference and click "Load Tokens" or press Enter to view tokens</div>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>
    </div>
</body>
</html>
