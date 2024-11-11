#!/bin/bash

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx

# Keep the container running
tail -f /var/log/nginx/error.log