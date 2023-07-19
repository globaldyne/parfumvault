#!/bin/bash

if [ ! -d "/config" ]; then
	mkdir -p /config
fi

echo "Setting enviroment"
touch /config/.DOCKER

if [ ! -f "/config/config.php" ]; then
	cp /html/inc/config.example.php /config/config.php
	ln -s /config/config.php /html/inc/config.php
else
	ln -s /config/config.php /html/inc/config.php
fi

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"
/usr/bin/php -S 0.0.0.0:8000 /html
