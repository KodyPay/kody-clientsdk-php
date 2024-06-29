#!/bin/sh
set -e
mkdir -p /run/php
chown -R www-data:www-data /run/php
php-fpm &
while [ ! -S /run/php/php-fpm.sock ]; do
    echo "Waiting for PHP-FPM socket..."
    sleep 1
done
nginx -g 'daemon off;'
exec "$@"
