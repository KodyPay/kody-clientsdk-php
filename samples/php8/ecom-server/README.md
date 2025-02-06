# Kody Store Demo

This project is a demo online store that integrates with KodyPay’s gRPC API for handling payments. It provides sample pages for:

- **Online Payment Demo:** Experience the online payment process.
- **Listing Store Payment Terminals:** View all payment terminals assigned to the store.
- **Listing Transactions:** Review all transactions made in the store.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting Started

1. **Setup Environment File:**
   Copy `.env.example` to `.env` and adjust the variables as needed.

2. **Build and Run:**
   Run the following command from the project root:
   ```bash
   docker-compose up --build
   ```

3. **Access the Application:**
   Open [http://localhost:8080](http://localhost:8080) in your browser.

## Development

- **Hot Reload:**
  Changes in the `public/` directory and `.env` file are automatically reflected in the container.

- **Run Commands Inside the Container:**
  For example, to open a shell:
  ```bash
  docker-compose exec php-fpm bash
  ```

- **Manage Composer Dependencies:**
  To install or update dependencies, run:
  ```bash
  docker-compose exec php-fpm composer install --optimize-autoloader --no-interaction
  ```
