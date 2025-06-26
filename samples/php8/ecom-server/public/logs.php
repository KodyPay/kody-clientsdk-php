<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KodyPay Logs</title>
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

        .log-entry {
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #fafafa;
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .log-event {
            font-weight: bold;
            color: #2c5aa0;
            font-size: 14px;
        }

        .log-timestamp {
            color: #666;
            font-size: 12px;
            text-align: right;
        }

        .log-content {
            margin-top: 10px;
        }

        .log-section {
            margin-bottom: 8px;
        }

        .log-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 3px;
        }

        .log-value {
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            padding: 8px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            white-space: pre-wrap;
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

        .no-logs {
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

        .refresh-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .refresh-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>KodyPay Logs</h1>

        <button id="refresh-btn" class="refresh-btn">Refresh Logs</button>

        <div id="logs-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Loading logs...</span>
            </div>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadLogs();

            // Add refresh button event listener
            document.getElementById('refresh-btn').addEventListener('click', function() {
                loadLogs();
            });
        });

        function loadLogs() {
            document.getElementById('logs-container').innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Loading logs...</span>
                </div>`;

            // Fetch logs via API
            fetch('/api/get_logs.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error, status = ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.logs) {
                        renderLogs(data.logs);
                    } else {
                        showError(data.error || 'Failed to load logs');
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    showError('Error: ' + error.message);
                });
        }

        function convertToLocalTimestamp(timestamp) {
            try {
                const date = new Date(timestamp);
                return date.toLocaleString();
            } catch (error) {
                return timestamp;
            }
        }

        function renderLogs(logs) {
            if (logs.length === 0) {
                document.getElementById('logs-container').innerHTML = '<div class="no-logs">No logs found</div>';
                return;
            }

            let html = '';

            logs.forEach(log => {
                // Convert timestamp to local time
                const timestamp = convertToLocalTimestamp(log.timestamp);

                html += `
                    <div class="log-entry">
                        <div class="log-header">
                            <div class="log-event">${escapeHtml(log.event)}</div>
                            <div class="log-timestamp">${timestamp}</div>
                        </div>

                        <div class="log-content">
                `;

                // Add Request if exists
                if (log.context && (log.context.Request || log.context.request)) {
                    html += `
                        <div class="log-section">
                            <div class="log-label">Request:</div>
                            <div class="log-value">${escapeHtml(log.context.Request || log.context.request)}</div>
                        </div>
                    `;
                }

                // Add Response if exists
                if (log.context && (log.context.Response || log.context.response)) {
                    html += `
                        <div class="log-section">
                            <div class="log-label">Response:</div>
                            <div class="log-value">${escapeHtml(log.context.Response || log.context.response)}</div>
                        </div>
                    `;
                }

                // Add Error if exists
                if (log.context && (log.context.Error || log.context.error)) {
                    html += `
                        <div class="log-section">
                            <div class="log-label">Error:</div>
                            <div class="log-value">${escapeHtml(log.context.Error || log.context.error)}</div>
                        </div>
                    `;
                }

                html += `
                        </div>
                    </div>
                `;
            });

            document.getElementById('logs-container').innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showError(message) {
            document.getElementById('logs-container').innerHTML =
                `<div class="error-message">${escapeHtml(message)}</div>`;
        }
    </script>
</body>
</html>
