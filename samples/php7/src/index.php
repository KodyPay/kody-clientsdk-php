<?php

require __DIR__ . '/../vendor/autoload.php';

use KodyBamboo\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use KodyBamboo\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

function helloWorld() {
    echo "Hello, World!";
}

helloWorld();


// Example of how you might use the gRPC client (this requires a running gRPC server)
$api_key = 'YOUR_API_KEY_HERE'; // Replace with your actual API key
$store_id = '1854502f-7e50-4633-8506-715690709643';

$client = new KodyPayTerminalServiceClient('https://grpc-staging.kodypay.com/', [
    'credentials' => ChannelCredentials::createInsecure()
]);

$request = new TerminalsRequest();
$request->setStoreId($store_id);

$metadata = [
    'X-API-Key' => [$api_key]
];

list($response, $status) = $client->Terminals($request, $metadata)->wait();

if ($status->code !== \Grpc\STATUS_OK) {
    echo "gRPC error: " . $status->details . PHP_EOL;
} else {
    echo "Terminals for Store ID: $store_id" . PHP_EOL;
    foreach ($response->getTerminals() as $terminal) {
        echo "Terminal ID: " . $terminal->getTerminalId() . " - Online: " . ($terminal->getOnline() ? 'Yes' : 'No') . PHP_EOL;
    }
}