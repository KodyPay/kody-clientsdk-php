<?php

require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

$kody_api_hostname = 'grpc.kodypay.com';
$store_id = '5fa2dd05-1805-494d-b843-fa1a7c34cf8a'; // Use your Kody store ID
$api_key = ''; // Put your API key

$client = new KodyPayTerminalServiceClient($kody_api_hostname, ['credentials' => ChannelCredentials::createSsl()]);
$metadata = ['X-API-Key' => [$api_key]];

echo "Requesting the list of terminals assigned to the store" . PHP_EOL;
$request = new TerminalsRequest();
$request->setStoreId($store_id);

list($response, $status) = $client->Terminals($request, $metadata)->wait();

if ($status->code !== \Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
} else {
    echo "Terminals for Store ID: $store_id" . PHP_EOL;
    foreach ($response->getTerminals() as $terminal) {
        echo "Terminal ID: " . $terminal->getTerminalId() . " - Online: " . ($terminal->getOnline() ? 'Yes' : 'No') . PHP_EOL;
    }
}