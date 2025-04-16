<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terminals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        #loading {
            font-size: 18px;
            text-align: center;
        }
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
    <script src="js/bubble.php"></script>
    <script>
        function fetchTerminals() {
            const tableBody = document.getElementById('terminals-body');

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
                    tableBody.innerHTML = '';

                    if (!data.terminals || data.terminals.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="3">No terminals available</td></tr>';
                        return;
                    }

                    data.terminals.forEach(terminal => {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td>${terminal.terminalId}</td><td>${terminal.online ? 'Yes' : 'No'}</td><td><a href="terminal_payment_form.php?tid=${terminal.terminalId}">Make a Payment</a></td>`;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error fetching terminals:', error);
                    tableBody.innerHTML = `<tr><td colspan="3">${error.message}</td></tr>`;
                });
        }

        setInterval(fetchTerminals, 5000);
        window.onload = fetchTerminals;
    </script>
</head>
<body>
<h1>Terminals</h1>
<table>
    <thead>
    <tr>
        <th>Terminal ID</th>
        <th>Online</th>
        <th>Payment</th>
    </tr>
    </thead>
    <tbody id="terminals-body">
    <!-- Data will be inserted here by JavaScript -->
    <tr id="loading"><td colspan="3">Loading...</td></tr>
    </tbody>
</table>
<div class="links">
    <a href="/index.php">Main menu</a>
</div>
</body>
</html>
