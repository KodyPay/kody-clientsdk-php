# Kody API – PHP SDK

This guide provides an overview of using the Kody PHP gRPC Client SDK and its reference documentation.

- [Client Libraries](#client-libraries)
- [PHP Installation](#php-installation)
- [Authentication](#authentication)
- [Documentation](#documentation)
- [Sample Code](#sample-code)

## Client Libraries

Kody provides client libraries for many popular languages to access the APIs. If your desired programming language is supported by the client libraries, we recommend that you use this option.

Available languages:
- Java: https://github.com/KodyPay/kody-clientsdk-java/
- Python: https://github.com/KodyPay/kody-clientsdk-python/
- PHP: https://github.com/KodyPay/kody-clientsdk-php/
- .Net: https://github.com/KodyPay/kody-clientsdk-dotnet/

The advantages of using the Kody Client library instead of a REST API are:
- Maintained by Kody.
- Built-in authentication and increased security.
- Built-in retries.
- Idiomatic for each language.
- Quicker development.
- Backwards compatibility with new versions.

If your coding language is not listed, please let the Kody team know and we will be able to create it for you.

## PHP Installation

### Requirements
- PHP 7.2 or later
- Composer
- gRPC PHP extension

### Step 1: Install via Composer

Add the following to your `composer.json`:

```json
{
  "require": {
    "kody/kody-php8-grpc-client": "v1.6.3"
  },
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "kody/kody-php8-grpc-client",
        "version": "v1.6.3",
        "dist": {
          "type": "zip",
          "url": "https://github.com/KodyPay/kody-clientsdk-php/releases/download/v1.6.3/kody-php8-grpc-package.zip"
        }
      }
    }
  ]
}
```

Then run:

```bash
composer install
```

### Step 2: Install gRPC and Protobuf PHP Extensions

#### macOS

```bash
pecl install grpc
```

#### Linux

```bash
sudo pecl install grpc
```

#### Windows

1. Download the gRPC and Protobuf extension DLLs from:
   - https://pecl.php.net/package/grpc
   - https://pecl.php.net/package/protobuf
2. Place the files in your PHP `ext` directory.
3. Add the following line to your `php.ini`:

```ini
extension=grpc.so
```

## Authentication

The client library uses a combination of a `Store ID` and an `API key`.

These credentials will be provided to you during the integration onboarding process. You will start with test credentials and receive live credentials upon launch.

### Host names

- Development and test: `https://grpc-staging.kodypay.com`
- Live: `https://grpc.kodypay.com`

## Documentation

For complete API documentation, examples, and integration guides, visit:
📚 https://api-docs.kody.com

## Sample Code

Here’s a simple example that uses the Kody PHP gRPC client:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Grpc\ChannelCredentials;

$kody_api_hostname = 'grpc.kodypay.com';
$store_id = 'your-store-id'; // Replace with your Store ID
$api_key = 'your-api-key';   // Replace with your API key

$client = new KodyPayTerminalServiceClient($kody_api_hostname, ['credentials' => ChannelCredentials::createSsl()]);
$metadata = ['X-API-Key' => [$api_key]];

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
```

### Run the Example

```bash
cd samples/php7
composer install
php src/index.php
```

## Troubleshooting

Ensure:
- PHP extensions for gRPC and Protobuf are installed and enabled.
- Composer dependencies are properly installed.
- Hostname and credentials are correct.

## Sample Code Repositories

- PHP: https://github.com/KodyPay/kody-clientsdk-php/tree/main/samples
- Java: https://github.com/KodyPay/kody-clientsdk-java/tree/main/samples
- Python: https://github.com/KodyPay/kody-clientsdk-python/tree/main/versions/3_12/samples
- .Net: https://github.com/KodyPay/kody-clientsdk-dotnet/tree/main/samples

## License

This project is licensed under the MIT License.
