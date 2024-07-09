<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$requiredEnvVars = ['KODY_HOSTNAME', 'KODY_STORE_ID', 'KODY_API_KEY'];

foreach ($requiredEnvVars as $envVar) {
    if (empty(getenv($envVar))) {
        throw new Exception("Environment variable $envVar is not set or empty");
    }
}

$config = [
    'hostname' => getenv('KODY_HOSTNAME') ?: 'grpc.kodypay.com',
    'store_id' => getenv('KODY_STORE_ID') ?: '',
    'api_key' => getenv('KODY_API_KEY') ?: '',
    'currency' => getenv('KODY_STORE_CURRENCY') ?: '',
    'redirect_url' => getenv('PAYMENT_REDIRECT_URL') ?: '',
];

return $config;
