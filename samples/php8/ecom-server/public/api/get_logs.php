<?php

header('Content-Type: application/json');

$config = require __DIR__ . '/../config.php';

use Com\Kodypay\Grpc\Utils\V1\LoggingServiceClient;
use Com\Kodypay\Grpc\Utils\V1\GetLogsRequest;
use Google\Protobuf\Timestamp;
use Grpc\ChannelCredentials;

$client = new LoggingServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
$metadata = ['X-API-Key' => [$config['api_key']]];

error_log("API hostname: " . $config['hostname']);
error_log("API storeId: " . $config['store_id']);
error_log("API key: " . $config['api_key']);

$request = new GetLogsRequest();
$request->setStoreId($config['store_id']);

// Optional: Add time range filters if provided via GET parameters
if (isset($_GET['start_time'])) {
    $startTime = new Timestamp();
    $startTime->fromDateTime(new DateTime($_GET['start_time']));
    $request->setStartTime($startTime);
}

if (isset($_GET['end_time'])) {
    $endTime = new Timestamp();
    $endTime->fromDateTime(new DateTime($_GET['end_time']));
    $request->setEndTime($endTime);
}

// Optional: Add event filter if provided
if (isset($_GET['event']) && !empty($_GET['event'])) {
    $request->setEvent($_GET['event']);
}

list($response, $status) = $client->GetLogs($request, $metadata)->wait();

if ($status->code !== \Grpc\STATUS_OK) {
    error_log("Status code: " . $status->code . " Details: " . $status->details);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch logs', 'details' => $status->details]);
    exit;
}

$logs = [];
foreach ($response->getLogs() as $logEntry) {
    $context = [];
    foreach ($logEntry->getContext() as $key => $value) {
        $context[$key] = $value;
    }

    $logs[] = [
        'id' => $logEntry->getId(),
        'timestamp' => $logEntry->getTimestamp()->toDateTime()->format('c'),
        'event' => $logEntry->getEvent(),
        'context' => $context
    ];
}

error_log("Logs loaded: " . count($logs) . " entries");
echo json_encode(['logs' => $logs]);
exit;
