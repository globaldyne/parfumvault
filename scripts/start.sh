#!/bin/bash

echo "Setting enviroment"
touch /config/.DOCKER

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"
/usr/bin/php -S 0.0.0.0:8000 /html
