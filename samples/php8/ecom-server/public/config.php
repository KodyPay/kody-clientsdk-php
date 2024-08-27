<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$requiredEnvVars = ['KODY_HOSTNAME', 'KODY_STORE_ID', 'KODY_API_KEY'];

foreach ($requiredEnvVars as $envVar) {
    if (empty($_ENV[$envVar])) {
        throw new Exception("Environment variable $envVar is not set or empty");
    }
}

$config = [
    'hostname' => $_ENV['KODY_HOSTNAME'] ?: 'grpc-staging.kodypay.com',
    'store_id' => $_ENV['KODY_STORE_ID'] ?: '',
    'api_key' => $_ENV['KODY_API_KEY'] ?: '',
    'currency' => $_ENV['KODY_STORE_CURRENCY'] ?: '',
    'redirect_url' => $_ENV['PAYMENT_REDIRECT_URL'] ?: '',
    'expiring_seconds' => $_ENV['PAYMENT_EXPIRING_SECONDS'] ?: '',
];

return $config;
