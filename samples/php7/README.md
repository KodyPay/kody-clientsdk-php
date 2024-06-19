# Kody PHP7 gRPC Sample

## Description
This project demonstrates how to use the Kody PHP7 gRPC client to communicate with Kody payments gateway.

## Requirements
- PHP 7.x
- Composer
- gRPC PHP extension
- Protobuf PHP extension

## Installation

### Step 1: Install Composer Dependencies
First, make sure you have Composer installed. Then, run the following command to install the required dependencies:

```bash
composer install
```

### Step 2: Install gRPC PHP Extension

#### macOS
To install the gRPC extension on macOS, use the following command:
```bash
pecl install grpc
```
Add the following line to your `php.ini` file:
```ini
extension=grpc.so
```

#### Linux
To install the gRPC extension on Linux, use the following command:
```bash
sudo pecl install grpc
```
Add the following line to your `php.ini` file:
```ini
extension=grpc.so
```

#### Windows
To install the gRPC extension on Windows:
1. Download the gRPC extension DLL from the PECL website: https://pecl.php.net/package/grpc
2. Move the downloaded file to the `ext` directory of your PHP installation.
3. Add the following line to your `php.ini` file:
```ini
extension=grpc
```

### Step 3: Install Protobuf PHP Extension

#### macOS
To install the Protobuf extension on macOS, use the following command:
```bash
pecl install protobuf
```
Add the following line to your `php.ini` file:
```ini
extension=protobuf.so
```

#### Linux
To install the Protobuf extension on Linux, use the following command:
```bash
sudo pecl install protobuf
```
Add the following line to your `php.ini` file:
```ini
extension=protobuf.so
```

#### Windows
To install the Protobuf extension on Windows:
1. Download the Protobuf extension DLL from the PECL website: https://pecl.php.net/package/protobuf
2. Move the downloaded file to the `ext` directory of your PHP installation.
3. Add the following line to your `php.ini` file:
```ini
extension=protobuf
```

## Troubleshooting
If you encounter issues, make sure:
- All required PHP extensions are installed and enabled.
- Your `composer.json` is correctly set up and all dependencies are installed.

## License
This project is licensed under the MIT License.
