<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminals</title>
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

        .no-terminals {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }

        .online-yes {
            color: #4CAF50;
            font-weight: bold;
        }

        .online-no {
            color: #e74c3c;
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
        }

        .payment-btn:hover {
            background-color: #45a049;
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
    <script>
        let isFirstLoad = true;

        function fetchTerminals() {
            const container = document.getElementById('terminals-container');

            // Only show loading spinner on first load
            if (isFirstLoad) {
                container.innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        <span>Loading terminals...</span>
                    </div>`;
            }

            fetch('api/terminals.php')
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error + ': ' + error.details);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.terminals || data.terminals.length === 0) {
                        container.innerHTML = '<div class="no-terminals">No terminals available</div>';
                        return;
                    }

                    // Build terminals table
                    let html = `
                        <table id="terminals-table">
                            <thead>
                                <tr>
                                    <th>Terminal ID</th>
                                    <th>Online Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.terminals.forEach(terminal => {
                        const onlineStatus = terminal.online ? 'Yes' : 'No';
                        const onlineClass = terminal.online ? 'online-yes' : 'online-no';

                        html += `
                            <tr>
                                <td>${terminal.terminalId}</td>
                                <td class="${onlineClass}">${onlineStatus}</td>
                                <td>
                                    <a href="terminal_payment_form.php?tid=${terminal.terminalId}" class="payment-btn">Make Payment</a>
                                </td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;
                    container.innerHTML = html;

                    // Mark first load as complete
                    isFirstLoad = false;
                })
                .catch(error => {
                    console.error('Error fetching terminals:', error);
                    // Only show error message, don't replace table if it's already loaded
                    if (isFirstLoad) {
                        container.innerHTML = `<div class="error-message">${escapeHtml(error.message)}</div>`;
                        isFirstLoad = false;
                    }
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-refresh every 5 seconds
        setInterval(fetchTerminals, 5000);
        window.onload = fetchTerminals;
    </script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Terminals</h1>

        <div id="terminals-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Loading terminals...</span>
            </div>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>
    </div>
</body>
</html>
