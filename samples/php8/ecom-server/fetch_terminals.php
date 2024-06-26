<?php

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

$client = new KodyPayTerminalServiceClient($config['hostname'], ['credentials' => ChannelCredentials::createSsl()]);
$metadata = ['X-API-Key' => [$config['api_key']]];

$request = new TerminalsRequest();
$request->setStoreId($config['store_id']);

list($response, $status) = $client->Terminals($request, $metadata)->wait();

if ($status->code !== \Grpc\STATUS_OK) {
    echo json_encode(['error' => $status->details]);
} else {
    $terminals = [];
    foreach ($response->getTerminals() as $terminal) {
        $terminals[] = [
            'terminalId' => $terminal->getTerminalId(),
            'online' => $terminal->getOnline() ? 'Yes' : 'No'
        ];
    }
    echo json_encode(['terminals' => $terminals]);
}
