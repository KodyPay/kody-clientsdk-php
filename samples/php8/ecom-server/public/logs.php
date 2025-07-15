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

        /* SDK Section Styles */
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
    <script src="js/sdk-common.php"></script>
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <a href="/index.php">← Back to Main Menu</a>
        </div>

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

        <div class="section-divider"></div>

        <div class="developer-section">
            <h2>🔧 KodyPay SDK Usage - Get Logs</h2>

            <div class="sdk-info">
                <h4>SDK Information</h4>
                <p><strong>Service:</strong> <code>LoggingService</code></p>
                <p><strong>Method:</strong> <code>GetLogs()</code></p>
                <p><strong>Request:</strong> <code>GetLogsRequest</code></p>
                <p><strong>Response:</strong> <code>GetLogsResponse</code></p>
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

use Com\Kodypay\Grpc\Utils\V1\LoggingServiceClient;
use Com\Kodypay\Grpc\Utils\V1\GetLogsRequest;
use Google\Protobuf\Timestamp;
use Grpc\ChannelCredentials;

// Configuration
$HOSTNAME = "grpc-staging.kodypay.com";
$API_KEY = "your-api-key";

// Step 1: Initialize SDK client with SSL credentials
$client = new LoggingServiceClient($HOSTNAME, [
    'credentials' => ChannelCredentials::createSsl()
]);

// Step 2: Set authentication headers with your API key
$metadata = ['X-API-Key' => [$API_KEY]];

// Step 3: Create GetLogsRequest and set required fields
$request = new GetLogsRequest();
$request->setStoreId('your-store-id');

// Step 4: Optional filters
// Filter by time range
$startTime = new Timestamp();
$startTime->fromDateTime(new DateTime('-1 hour')); // Last hour
$request->setStartTime($startTime);

$endTime = new Timestamp();
$endTime->fromDateTime(new DateTime('now'));
$request->setEndTime($endTime);


// Step 5: Call GetLogs() method and wait for response
list($response, $status) = $client->GetLogs($request, $metadata)->wait();

// Step 6: Handle gRPC response status
if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit;
}

// Step 7: Process response
$logs = $response->getLogs();
echo "Found " . count($logs) . " log entries:" . PHP_EOL;

foreach ($logs as $logEntry) {
    echo "ID: " . $logEntry->getId() . PHP_EOL;
    echo "Timestamp: " . $logEntry->getTimestamp()->toDateTime()->format('Y-m-d H:i:s') . PHP_EOL;
    echo "Event: " . $logEntry->getEvent() . PHP_EOL;

    // Process context data
    $context = $logEntry->getContext();
    if (!empty($context)) {
        echo "Context:" . PHP_EOL;
        foreach ($context as $key => $value) {
            echo "  $key: $value" . PHP_EOL;
        }
    }
    echo "---" . PHP_EOL;
}
?&gt;</code></pre>
                    </div>
                </div>

                <!-- Java Tab -->
                <div id="java-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('java-code')">Copy</button>
                        <pre id="java-code"><code>import com.kodypay.grpc.utils.v1.LoggingServiceGrpc;
import com.kodypay.grpc.utils.v1.GetLogsRequest;
import com.kodypay.grpc.utils.v1.GetLogsResponse;
import com.google.protobuf.Timestamp;
import io.grpc.ManagedChannelBuilder;
import io.grpc.Metadata;
import io.grpc.stub.MetadataUtils;
import java.time.Instant;
import java.time.temporal.ChronoUnit;

public class GetLogsExample {
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
        var client = LoggingServiceGrpc.newBlockingStub(channel)
            .withInterceptors(MetadataUtils.newAttachHeadersInterceptor(metadata));

        // Step 3: Create GetLogsRequest and set required fields
        var requestBuilder = GetLogsRequest.newBuilder()
            .setStoreId("your-store-id");

        // Step 4: Optional filters
        // Filter by time range (last hour)
        Instant now = Instant.now();
        Instant oneHourAgo = now.minus(1, ChronoUnit.HOURS);

        Timestamp startTime = Timestamp.newBuilder()
            .setSeconds(oneHourAgo.getEpochSecond())
            .build();
        Timestamp endTime = Timestamp.newBuilder()
            .setSeconds(now.getEpochSecond())
            .build();

        requestBuilder.setStartTime(startTime)
                     .setEndTime(endTime);


        GetLogsRequest request = requestBuilder.build();

        // Step 5: Call GetLogs() method and get response
        GetLogsResponse response = client.getLogs(request);

        // Step 6: Process response
        System.out.println("Found " + response.getLogsCount() + " log entries:");

        response.getLogsList().forEach(logEntry -> {
            System.out.println("ID: " + logEntry.getId());
            System.out.println("Timestamp: " + logEntry.getTimestamp());
            System.out.println("Event: " + logEntry.getEvent());

            // Process context data
            if (!logEntry.getContextMap().isEmpty()) {
                System.out.println("Context:");
                logEntry.getContextMap().forEach((key, value) -> {
                    System.out.println("  " + key + ": " + value);
                });
            }
            System.out.println("---");
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
from datetime import datetime, timedelta
import kody_clientsdk_python.utils.v1.logging_pb2 as logging_model
import kody_clientsdk_python.utils.v1.logging_pb2_grpc as logging_client
from google.protobuf.timestamp_pb2 import Timestamp

def get_logs():
    # Configuration
    HOSTNAME = "grpc-staging.kodypay.com:443"
    API_KEY = "your-api-key"

    # Step 1: Create secure channel
    channel = grpc.secure_channel(HOSTNAME, grpc.ssl_channel_credentials())

    # Step 2: Create client and set metadata with API key
    client = logging_client.LoggingServiceStub(channel)
    metadata = [("x-api-key", API_KEY)]

    # Step 3: Create GetLogsRequest and set required fields
    request = logging_model.GetLogsRequest(
        store_id="your-store-id"
    )

    # Step 4: Optional filters
    # Filter by time range (last hour)
    end_time = datetime.now()
    start_time = end_time - timedelta(hours=1)

    start_timestamp = Timestamp()
    start_timestamp.FromDatetime(start_time)
    request.start_time.CopyFrom(start_timestamp)

    end_timestamp = Timestamp()
    end_timestamp.FromDatetime(end_time)
    request.end_time.CopyFrom(end_timestamp)


    # Step 5: Call GetLogs() method and get response
    response = client.GetLogs(request, metadata=metadata)

    # Step 6: Process response
    logs = response.logs
    print(f"Found {len(logs)} log entries:")

    for log_entry in logs:
        print(f"ID: {log_entry.id}")
        print(f"Timestamp: {log_entry.timestamp.ToDatetime()}")
        print(f"Event: {log_entry.event}")

        # Process context data
        if log_entry.context:
            print("Context:")
            for key, value in log_entry.context.items():
                print(f"  {key}: {value}")
        print("---")

if __name__ == "__main__":
    try:
        get_logs()
    except grpc.RpcError as e:
        print(f"gRPC Error: {e.code()} - {e.details()}")
    except Exception as e:
        print(f"Exception: {e}")</code></pre>
                    </div>
                </div>

                <!-- .NET Tab -->
                <div id="dotnet-content" class="tab-content">
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode('dotnet-code')">Copy</button>
                        <pre id="dotnet-code"><code>using Grpc.Core;
using Grpc.Net.Client;
using Com.Kodypay.Grpc.Utils.V1;
using Google.Protobuf.WellKnownTypes;

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
        var client = new LoggingService.LoggingServiceClient(channel);

        // Step 3: Set authentication headers with API key
        var metadata = new Metadata
        {
            { "X-API-Key", API_KEY }
        };

        // Step 4: Create GetLogsRequest and set required fields
        var request = new GetLogsRequest
        {
            StoreId = "your-store-id"
        };

        // Step 5: Optional filters
        // Filter by time range (last hour)
        var endTime = DateTime.UtcNow;
        var startTime = endTime.AddHours(-1);

        request.StartTime = Timestamp.FromDateTime(startTime);
        request.EndTime = Timestamp.FromDateTime(endTime);


        try
        {
            // Step 6: Call GetLogs() method and get response
            var response = await client.GetLogsAsync(request, metadata);

            // Step 7: Process response
            Console.WriteLine($"Found {response.Logs.Count} log entries:");

            foreach (var logEntry in response.Logs)
            {
                Console.WriteLine($"ID: {logEntry.Id}");
                Console.WriteLine($"Timestamp: {logEntry.Timestamp.ToDateTime()}");
                Console.WriteLine($"Event: {logEntry.Event}");

                // Process context data
                if (logEntry.Context.Count > 0)
                {
                    Console.WriteLine("Context:");
                    foreach (var kvp in logEntry.Context)
                    {
                        Console.WriteLine($"  {kvp.Key}: {kvp.Value}");
                    }
                }
                Console.WriteLine("---");
            }
        }
        catch (RpcException ex)
        {
            Console.WriteLine($"gRPC Error: {ex.Status.StatusCode} - {ex.Status.Detail}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Exception: {ex.Message}");
        }
    }
}</code></pre>
                    </div>
                </div>
            </div>
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
