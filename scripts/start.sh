#!/bin/bash

if [ ! -d "/config" ]; then
	mkdir -p /config
fi

echo "Setting enviroment"
touch /config/.DOCKER
mkdir -p /run/php-fpm

if [ ! -f "/config/config.php" ]; then
	cp /html/inc/config.example.php /config/config.php
	ln -s /config/config.php /html/inc/config.php
else
	ln -s /config/config.php /html/inc/config.php
fi

echo "Setting permissions"
chown -R apache:apache /html
chown -R apache:apache /config

echo "Starting web server"
/usr/sbin/php-fpm
/usr/sbin/httpd -k start
echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
touch /var/log/php-fpm/www-error.log
tail -f /var/log/php-fpm/www-error.log /var/log/httpd/error_log

