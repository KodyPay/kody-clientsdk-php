<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminals - KodyPay SDK Demo</title>
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

        /* Developer Section Styles */
        .developer-section {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .developer-section h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .code-section {
            margin: 30px 0;
        }

        .code-section h3 {
            color: #555;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .tab-button {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            padding: 8px 16px;
            cursor: pointer;
            margin-right: 4px;
            border-radius: 4px 4px 0 0;
            color: #555;
            font-size: 14px;
            display: inline-block;
        }

        .tab-button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .code-block {
            position: relative;
            background: #2d3748;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .code-block pre {
            margin: 0;
            padding: 20px;
            color: #e2e8f0;
            background: #2d3748;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            z-index: 10;
            transition: background-color 0.2s;
        }

        .copy-btn:hover {
            background: #0056b3;
        }

        .copy-btn.copied {
            background: #28a745;
        }

        .sdk-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }

        .sdk-info h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }

        .sdk-info p {
            margin: 5px 0;
            color: #555;
        }

        .section-divider {
            border-top: 1px solid #ddd;
            margin: 40px 0;
        }
    </style>
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

        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            const code = codeElement.textContent || codeElement.innerText;

            navigator.clipboard.writeText(code).then(function() {
                // Visual feedback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = code;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Visual feedback for fallback
                const button = codeElement.parentElement.querySelector('.copy-btn');
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            });
        }

        function showTab(language) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            const selectedContent = document.getElementById(language + '-content');
            if (selectedContent) {
                selectedContent.classList.add('active');
            }

            // Add active class to clicked button
            const selectedButton = document.querySelector(`[onclick="showTab('${language}')"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }

        // Auto-refresh every 5 seconds
        setInterval(fetchTerminals, 5000);
        window.onload = function() {
            fetchTerminals();
            // Show PHP tab by default
            showTab('php');
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

        <h1>Terminals - KodyPay SDK Demo</h1>

        <div id="terminals-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Loading terminals...</span>
            </div>
        </div>

        <div class="links">
            <a href="/index.php">Main menu</a>
        </div>

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Terminals</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>KodyPayTerminalService</code></p>
                <p><strong>Method:</strong> <code>Terminals()</code></p>
                <p><strong>Request:</strong> <code>TerminalsRequest</code></p>
                <p><strong>Response:</strong> <code>TerminalsResponse</code></p>
            </div>

            <div class="code-section">
                <h3>SDK Examples</h3>

                <div class="tabs">
                    <button class="tab-button" onclick="showTab('php')">PHP</button>
                    <button class="tab-button" onclick="showTab('java')">Java</button>
                    <button class="tab-button" onclick="showTab('python')">Python</button>
                    <button class="tab-button" onclick="showTab('dotnet')">.NET</button>
                </div>

                <!-- PHP Tab -->
                <div id="php-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('php-code')">Copy</button>
                        <pre id="php-code"><code>&lt;?php
require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

// Configuration
$HOSTNAME = "grpc-staging.kodypay.com";
$API_KEY = "your-api-key";

// Step 1: Initialize SDK client with SSL credentials
$client = new KodyPayTerminalServiceClient($HOSTNAME, [
    'credentials' => ChannelCredentials::createSsl()
]);

// Step 2: Set authentication headers with your API key
$metadata = ['X-API-Key' => [$API_KEY]];

// Step 3: Create TerminalsRequest and set store ID
$request = new TerminalsRequest();
$request->setStoreId('your-store-id');

// Step 4: Call Terminals() method and wait for response
list($response, $status) = $client->Terminals($request, $metadata)->wait();

// Step 5: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 6: Process terminals from response
foreach ($response->getTerminals() as $terminal) {
    echo "Terminal ID: " . $terminal->getTerminalId() . PHP_EOL;
    echo "Online: " . ($terminal->getOnline() ? 'Yes' : 'No') . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.pay.v1.KodyPayTerminalServiceGrpc;
import com.kodypay.grpc.pay.v1.TerminalsRequest;
import com.kodypay.grpc.pay.v1.TerminalsResponse;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;

public class ListTerminalsExample {
    public static final String HOSTNAME = "grpc-staging.kodypay.com";
    public static final String API_KEY = "your-api-key";

    public static void main(String[] args) {
        // Step 1: Create metadata with API key
        Metadata metadata = new Metadata();
        metadata.put(Metadata.Key.of("X-API-Key", Metadata.ASCII_STRING_MARSHALLER), API_KEY);

        // Step 2: Build secure channel and create client
        var channel = ManagedChannelBuilder.forAddress(HOSTNAME, 443)
            .useTransportSecurity()
            .build();
        var client = KodyPayTerminalServiceGrpc.newBlockingStub(channel)
            .withInterceptors(MetadataUtils.newAttachHeadersInterceptor(metadata));

        // Step 3: Create TerminalsRequest and set store ID
        TerminalsRequest request = TerminalsRequest.newBuilder()
            .setStoreId("your-store-id")
            .build();

        // Step 4: Call Terminals() method and get response
        TerminalsResponse response = client.terminals(request);

        // Step 5: Process terminals from response
        response.getTerminalsList().forEach(terminal -> {
            System.out.println("Terminal ID: " + terminal.getTerminalId());
            System.out.println("Online: " + terminal.getOnline());
        });
    }
}</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div id="python-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('python-code')">Copy</button>
                        <pre id="python-code"><code>import grpc
import kody_clientsdk_python.pay.v1.pay_pb2 as kody_model
import kody_clientsdk_python.pay.v1.pay_pb2_grpc as kody_client

def list_terminals():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = kody_client.KodyPayTerminalServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create TerminalsRequest and set store ID
    request = kody_model.TerminalsRequest(store_id="your-store-id")

    # Step 4: Call Terminals() method and get response
    response = client.Terminals(request, metadata=metadata)

    # Step 5: Process terminals from response
    for terminal in response.terminals:
        print(f"Terminal ID: {terminal.terminal_id}")
        print(f"Online: {terminal.online}")

if __name__ == "__main__":
    list_terminals()</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('dotnet-code')">Copy</button>
                        <pre id="dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Pay.V1;

class Program
{
    static async Task Main(string[] args)
    {
        // Configuration
        var HOSTNAME = "grpc-staging.kodypay.com";
        var API_KEY = "your-api-key";

        // Step 1: Create secure channel
        var channel = GrpcChannel.ForAddress("https://" + HOSTNAME);

        // Step 2: Create client
        var client = new KodyPayTerminalService.KodyPayTerminalServiceClient(channel);

        // Step 3: Set authentication headers with API key
        var metadata = new Metadata
        {
            { "X-API-Key", API_KEY }
        };

        // Step 4: Create TerminalsRequest and set store ID
        var request = new TerminalsRequest { StoreId = "your-store-id" };

        // Step 5: Call Terminals() method and get response
        var response = await client.TerminalsAsync(request, metadata);

        // Step 6: Process terminals from response
        foreach (var terminal in response.Terminals)
        {
            Console.WriteLine($"Terminal ID: {terminal.TerminalId}");
            Console.WriteLine($"Online: {terminal.Online}");
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
