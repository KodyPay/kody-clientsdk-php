<?php

$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

$client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
$metadata = ['X-API-Key' => [$config['api_key']]];

error_log("API hostname: ".$config['hostname']);
error_log("API storeId: ".$config['store_id']);
error_log("API key: ".$config['api_key']);
error_log("Currency: ".$config['currency']);

$request = new TerminalsRequest();
$request->setStoreId($config['store_id']);

list($response, $status) = $client->Terminals($request, $metadata)->wait();

if ($status->code !== \Grpc\STATUS_OK) {
    error_log("Status code: ".$status->code." Details: ".$status->details);
    echo json_encode(['error' => $status->details]);
} else {
    $terminals = [];
    foreach ($response->getTerminals() as $terminal) {
        $terminals[] = [
            'terminalId' => $terminal->getTerminalId(),
            'online' => $terminal->getOnline() ? 'Yes' : 'No'
        ];
    }
    error_log("Terminals loaded");
    echo json_encode(['terminals' => $terminals]);
}
