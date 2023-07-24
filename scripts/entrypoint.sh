#!/bin/bash

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"

if [ ! -d $TMP_PATH ] 
then
    echo "Temp directory not exists, creating $TMP_PATH." 
    mkdir -p $TMP_PATH
fi

php-fpm
nginx -e /tmp/error.log
tail -f /tmp/*.log
