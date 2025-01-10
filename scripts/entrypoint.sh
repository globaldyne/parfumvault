#!/bin/bash

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"



mkdir /tmp/php

if [ ! -d $TMP_PATH ] 
then
    echo "Temp directory not exists, creating $TMP_PATH." 
    mkdir -p $TMP_PATH
fi

php-fpm
nginx -e /tmp/error.log
if ps aux | grep -q "[n]ginx"; then
    echo "Server is ready"
else
    echo "Failed to start nginx."
fi
touch /tmp/php-fpm-www-error.log
tail -f /tmp/php-fpm-www-error.log

/usr/bin/add_role_column.sh
