#!/bin/bash

if [ ! -d "/config" ]; then
	mkdir -p /config
fi

echo "Setting enviroment"
touch /config/.DOCKER
mkdir -p /tmp/php-fpm
mkdir -p /tmp/log/php-fpm
mkdir -p /tmp/lib/php-fpm

if [ ! -f "/config/config.php" ]; then
	cp /html/inc/config.example.php /config/config.php
	ln -s /config/config.php /html/inc/config.php
else
	ln -s /config/config.php /html/inc/config.php
fi

echo "Starting web server"
/usr/sbin/php-fpm
/usr/sbin/httpd -k start
echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
touch /tmp/log/php-fpm/www-error.log
tail -f /tmp/log/php-fpm/www-error.log
