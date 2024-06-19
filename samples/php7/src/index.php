<?php

require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

$api_key = 'SAKtKjZSzKMy5vSw4ihZtt5YJU1FNoaGIyNz4PJ7vCTg'; // Replace with your actual API key
$store_id = '5fa2dd05-1805-494d-b843-fa1a7c34cf8a';

$client = new KodyPayTerminalServiceClient('grpc.kodypay.com', [
    'credentials' => ChannelCredentials::createInsecure()
]);


$request = new TerminalsRequest();

$request->setStoreId($store_id);

$metadata = [
    'X-API-Key' => [$api_key]
];

echo "Making gRPC request\n";
list($response, $status) = $client->Terminals($request, $metadata)->wait();

echo "Server response\n";

if ($status->code !== \Grpc\STATUS_OK) {
    echo "gRPC error: " . $status->details . PHP_EOL;
} else {
    echo "Terminals for Store ID: $store_id" . PHP_EOL;
    foreach ($response->getTerminals() as $terminal) {
        echo "Terminal ID: " . $terminal->getTerminalId() . " - Online: " . ($terminal->getOnline() ? 'Yes' : 'No') . PHP_EOL;
    }
}