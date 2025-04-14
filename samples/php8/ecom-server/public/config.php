<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$requiredEnvVars = ['KODY_HOSTNAME', 'KODY_STORE_ID', 'KODY_API_KEY'];

foreach ($requiredEnvVars as $envVar) {
    if (empty($_ENV[$envVar])) {
        throw new Exception("Environment variable $envVar is not set or empty");
    }
}

// Use cookie values if they exist
$storeIdFromCookie = isset($_COOKIE['store_id']) ? $_COOKIE['store_id'] : '';
$apiKeyFromCookie = isset($_COOKIE['api_key']) ? $_COOKIE['api_key'] : '';

$config = [
    'hostname' => $_ENV['KODY_HOSTNAME'] ?: 'grpc-staging.kodypay.com',
    'store_id' => $storeIdFromCookie ?: $_ENV['KODY_STORE_ID'] ?: '',
    'api_key' => $apiKeyFromCookie ?: $_ENV['KODY_API_KEY'] ?: '',
    'currency' => $_ENV['KODY_STORE_CURRENCY'] ?: '',
    'currencies' => $_ENV['KODY_STORE_CURRENCIES'] ? explode(',', $_ENV['KODY_STORE_CURRENCIES']) : [],
    'redirect_url' => $_ENV['PAYMENT_REDIRECT_URL'] ?: '',
    'expiring_seconds' => $_ENV['PAYMENT_EXPIRING_SECONDS'] ?: 900,
];

return $config;
