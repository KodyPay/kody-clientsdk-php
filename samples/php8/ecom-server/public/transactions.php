<?php
$functions = require_once __DIR__ . '/functions.php';

// Initial page for rendering
$currentPage = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Transactions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status-SUCCESS {
            color: green;
        }
        .status-FAILED {
            color: red;
        }
        .status-PENDING {
            color: orange;
        }
        .total-count {
            margin-bottom: 20px;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination .active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
        .pagination .disabled {
            color: #aaa;
            pointer-events: none;
            border: 1px solid #ddd;
        }
        button.refund-btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            background-color: white;
            margin: 0 4px;
            cursor: pointer;
            border-radius: 4px;
        }

        button.refund-btn:hover {
            background-color: #ddd;
        }

        button.refund-btn:disabled {
            color: #aaa;
            border-color: #ddd;
            background-color: #f9f9f9;
            cursor: not-allowed;
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
        }
    </style>
</head>
<body>
    <h1>Payment Transactions</h1>

    <div id="total-count" class="total-count">Loading transactions...</div>

    <div id="payments-container">
        <div class="loading">
            <div class="spinner"></div>
            <span>Loading payment data...</span>
        </div>
    </div>

    <div id="pagination" class="pagination"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = <?php echo $currentPage; ?>;
            const pageSize = 16;

            loadPayments(currentPage);

            function loadPayments(page) {
                document.getElementById('payments-container').innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        <span>Loading payment data...</span>
                    </div>`;
                document.getElementById('pagination').innerHTML = '';
                document.getElementById('total-count').textContent = 'Loading transactions...';

                // Fetch payments via API
                fetch('/api/payments.php?page=' + page + '&pageSize=' + pageSize)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error, status = ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            renderPayments(data.payments, data.pagination);
                        } else {
                            showError(data.errorMessage || 'Failed to load payments');
                        }
                    })
                    .catch(error => {
                        showError('Error: ' + error.message);
                    });
            }

            function renderPayments(payments, pagination) {
                // Update pagination info
                document.getElementById('total-count').textContent =
                    'Total Transactions: ' + pagination.totalItems +
                    ' (Page ' + (pagination.currentPage + 1) + ' of ' + pagination.totalPages + ')';

                if (payments.length === 0) {
                    document.getElementById('payments-container').innerHTML = '<p>No payments found.</p>';
                    return;
                }

                // Create table
                let html = `
                    <table id="payments-table">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Reference</th>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Paid Date</th>
                                <th>Paid Amount</th>
                                <th>Refund</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Add payment rows
                payments.forEach(payment => {
                    const isSuccess = payment.status_text === 'SUCCESS';

                    html += `
                        <tr data-payment-id="${payment.payment_id}" data-status="${payment.status_text}">
                            <td>${payment.payment_id}</td>
                            <td>${payment.payment_reference}</td>
                            <td>${payment.order_id}</td>
                            <td class="status-${payment.status_text}">${payment.status_text}</td>
                            <td>${payment.date_created || 'N/A'}</td>
                            <td>${payment.date_paid || 'N/A'}</td>
                            <td class="paid-amount"></td>
                            <td>
                                ${isSuccess ?
                                    `<form action="refund-form.php" method="GET">
                                        <input type="hidden" name="payment_id" value="${payment.payment_id}">
                                        <button type="submit" class="refund-btn">Refund</button>
                                    </form>` : ''}
                            </td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                document.getElementById('payments-container').innerHTML = html;

                // Add pagination
                renderPagination(pagination);

                // Load payment amounts only for SUCCESS transactions
                const rows = document.querySelectorAll('#payments-table tbody tr[data-status="SUCCESS"]');
                rows.forEach(row => {
                    const paymentId = row.getAttribute('data-payment-id');
                    if (paymentId) {
                        fetchPaymentDetails(paymentId, row);
                    }
                });
            }

            function renderPagination(pagination) {
                let html = '';

                // Previous button
                if (pagination.currentPage > 0) {
                    html += `<a href="?page=${pagination.currentPage - 1}">Previous</a>`;
                } else {
                    html += `<span class="disabled">Previous</span>`;
                }

                // Current page indicator
                html += `<span class="active">${pagination.currentPage + 1}</span>`;

                // Next button
                if (pagination.currentPage < pagination.totalPages - 1) {
                    html += `<a href="?page=${pagination.currentPage + 1}">Next</a>`;
                } else {
                    html += `<span class="disabled">Next</span>`;
                }

                document.getElementById('pagination').innerHTML = html;

                // Add event listeners to pagination links
                document.querySelectorAll('#pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const newPage = url.searchParams.get('page');
                        loadPayments(parseInt(newPage));

                        // Update URL without reloading the page
                        window.history.pushState({}, '', `?page=${newPage}`);
                    });
                });
            }

            function fetchPaymentDetails(paymentId, row) {
                console.log("Fetching details for payment ID: " + paymentId);

                fetch('/api/payment_details.php?payment_id=' + encodeURIComponent(paymentId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error, status = ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Received response for payment ID: " + paymentId);

                        if (data.success) {
                            if (data.saleData && data.saleData.amount && data.saleData.currency) {
                                const amountText = data.saleData.amount + ' ' + data.saleData.currency;
                                row.querySelector('.paid-amount').textContent = amountText;
                                console.log("Amount set: " + amountText);
                            } else {
                                row.querySelector('.paid-amount').textContent = '';
                                console.log("Missing amount or currency in response");
                            }
                        } else {
                            row.querySelector('.paid-amount').textContent = '';
                            console.log("API call unsuccessful: " + JSON.stringify(data));
                        }
                    })
                    .catch(error => {
                        console.log("Error fetching payment details: " + error.message);
                    });
            }

            function showError(message) {
                document.getElementById('payments-container').innerHTML =
                    `<div class="error-message">${message}</div>`;
                document.getElementById('total-count').textContent = 'Error loading transactions';
            }
        });
    </script>
</body>
</html>
