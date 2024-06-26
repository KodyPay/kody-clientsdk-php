<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terminals</title>
    <style>
        table {
            width: 50%;
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
    </style>
    <script>
        function fetchTerminals() {
            fetch('fetch_terminals.php')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('terminals-body');
                    tableBody.innerHTML = '';

                    data.terminals.forEach(terminal => {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td>${terminal.terminalId}</td><td>${terminal.online}</td>`;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching terminals:', error));
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
    </tr>
    </thead>
    <tbody id="terminals-body">
    <!-- Data will be inserted here by JavaScript -->
    </tbody>
</table>
</body>
</html>
