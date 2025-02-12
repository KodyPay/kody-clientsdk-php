# Kody PHP gRPC Client

## Description
The Kody PHP gRPC Client is an SDK generated from protobuf protocols to facilitate communication with the Kody Payments Gateway. This library provides a simple and efficient way to integrate Kody payment functionalities into your PHP applications.

## Requirements
- PHP 7.2 or later
- Composer
- gRPC PHP extension

## Installation

### Step 1: Install via Composer
To install the Kody PHP gRPC Client, simply add it to your project's `composer.json` file and run `composer install`.

```json
{
    "require": {
        "kody/kody-php8-grpc-client": "v1.5.4"
    },
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "kody/kody-php8-grpc-client",
                "version": "v1.5.4",
                "dist": {
                    "type": "zip",
                    "url": "https://github.com/KodyPay/kody-clientsdk-php/releases/download/v1.5.4/kody-php8-grpc-package.zip"
                }
            }
        }
    ]
}
```

Run the following command to install the dependencies:
```bash
composer install
```

### Step 2: Install gRPC and Protobuf PHP Extensions

#### macOS
To install the gRPC extensions on macOS, use the following command:
```bash
pecl install grpc
```

#### Linux
To install the gRPC and Protobuf extensions on Linux, use the following commands:
```bash
sudo pecl install grpc
```

#### Windows
To install the gRPC and Protobuf extensions on Windows:
1. Download the gRPC and Protobuf extension DLLs from the PECL website: https://pecl.php.net/package/grpc and https://pecl.php.net/package/protobuf
2. Move the downloaded files to the `ext` directory of your PHP installation.

Add the following lines to your `php.ini` file if you are running a live server:
```ini
extension=grpc.so
```

## Usage

### Example Script
Here is an example of how to use the Kody PHP7 gRPC client to communicate with the Kody Payments Gateway:

```php
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
```

### Running the Example
Make sure you have completed the installation steps above, then run the example script:

```bash
cd samples/php7
composer install
php src/index.php
```

## Troubleshooting
If you encounter issues, ensure:
- All required PHP extensions are installed and enabled.
- Your `composer.json` is correctly set up and all dependencies are installed.
- Contact Kody support or tech team

## License
This project is licensed under the MIT License.
