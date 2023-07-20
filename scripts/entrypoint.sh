#!/bin/bash

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"
php-fpm
nginx -e /tmp/error.log
tail -f /tmp/*.log
