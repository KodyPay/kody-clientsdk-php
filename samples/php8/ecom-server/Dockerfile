# Use the official PHP 8.3 FPM image
FROM php:8.3-fpm

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-install zip \
    && pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /application

# Copy the application code
COPY . /application

# Allow Composer to run as a superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install PHP dependencies using Composer
RUN composer install --optimize-autoloader --no-interaction --no-progress

# Copy the Nginx configuration file
COPY nginx.conf /etc/nginx/nginx.conf

# Create the PHP-FPM run directory
RUN mkdir -p /run/php && chown -R www-data:www-data /run/php

# Copy the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80 for Nginx
EXPOSE 80

# Set the entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
