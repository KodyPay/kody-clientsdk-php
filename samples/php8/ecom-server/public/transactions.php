<?php
$functions = require_once __DIR__ . '/functions.php';
$currentPage = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transactions</title>
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

        .total-count {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
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

        .status-SUCCESS {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-FAILED {
            color: #e74c3c;
            font-weight: bold;
        }

        .status-PENDING {
            color: #ff9800;
            font-weight: bold;
        }

        .refund-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .refund-btn:hover {
            background-color: #45a049;
        }

        .refund-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination a:hover {
            background-color: #f8f9fa;
        }

        .pagination .disabled {
            color: #aaa;
            pointer-events: none;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
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

        .no-payments {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
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
    </style>
    <script src="js/bubble.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Payment Transactions</h1>

        <div id="total-count" class="total-count">Loading transactions...</div>

        <div id="payments-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Loading payment data...</span>
            </div>
        </div>

        <div id="pagination" class="pagination"></div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>
    </div>

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

                // Fetch payments from API
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
                // Update pagination display
                document.getElementById('total-count').textContent =
                    'Total Transactions: ' + pagination.totalItems +
                    ' (Page ' + (pagination.currentPage + 1) + ' of ' + pagination.totalPages + ')';

                if (payments.length === 0) {
                    document.getElementById('payments-container').innerHTML = '<div class="no-payments">No payments found</div>';
                    return;
                }

                let html = `
                    <div class="table-container">
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

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
                            <td class="paid-amount">-</td>
                            <td>
                                ${isSuccess ?
                                    `<form action="refund-form.php" method="GET" style="margin: 0;">
                                        <input type="hidden" name="payment_id" value="${payment.payment_id}">
                                        <button type="submit" class="refund-btn">Refund</button>
                                    </form>` : '-'}
                            </td>
                        </tr>
                    `;
                });

                html += `</tbody></table></div>`;
                document.getElementById('payments-container').innerHTML = html;

                renderPagination(pagination);

                // Load amounts for successful payments only
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

                if (pagination.currentPage > 0) {
                    html += `<a href="?page=${pagination.currentPage - 1}">Previous</a>`;
                } else {
                    html += `<span class="disabled">Previous</span>`;
                }

                html += `<span class="active">${pagination.currentPage + 1}</span>`;

                if (pagination.currentPage < pagination.totalPages - 1) {
                    html += `<a href="?page=${pagination.currentPage + 1}">Next</a>`;
                } else {
                    html += `<span class="disabled">Next</span>`;
                }

                document.getElementById('pagination').innerHTML = html;

                // Add click handlers for pagination
                document.querySelectorAll('#pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const newPage = url.searchParams.get('page');
                        loadPayments(parseInt(newPage));
                        window.history.pushState({}, '', `?page=${newPage}`);
                    });
                });
            }

            function fetchPaymentDetails(paymentId, row) {
                fetch('/api/payment_details.php?payment_id=' + encodeURIComponent(paymentId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error, status = ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.saleData && data.saleData.amount && data.saleData.currency) {
                            const amountText = data.saleData.amount + ' ' + data.saleData.currency;
                            row.querySelector('.paid-amount').textContent = amountText;
                        } else {
                            row.querySelector('.paid-amount').textContent = 'N/A';
                        }
                    })
                    .catch(error => {
                        row.querySelector('.paid-amount').textContent = 'Error';
                    });
            }

            function showError(message) {
                document.getElementById('payments-container').innerHTML =
                    `<div class="error-message">${escapeHtml(message)}</div>`;
                document.getElementById('total-count').textContent = 'Error loading transactions';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
</body>
</html>
