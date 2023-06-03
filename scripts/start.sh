#!/bin/bash

MYSQL_DB="pvault"
MYSQL_USER="pvault"
MYSQL_PASS="pvault"
DATADIR="/var/lib/mysql"

if [ ! -d "/config" ]; then
	mkdir -p /config
fi

if [ ! -d "$DATADIR/mysql" ]; then
	echo "Initializing DB"
	mysql_install_db --user=mysql --ldata=/var/lib/mysql > /tmp/my.log 2>&1
	/usr/bin/mysqld_safe  --init-file=/tmp/mysql-first-time.sql & >> /tmp/my.log 2>&1
	echo "Waiting for DB to start"
	sleep 10
	echo "Importing default schema"
	mysql -u$MYSQL_USER -p$MYSQL_PASS $MYSQL_DB < /html/db/pvault.sql
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
chown -R mysql:mysql /var/lib/mysql
chown -R apache:apache /html
chown -R apache:apache /config

echo "Starting DB"
/usr/bin/mysqld_safe &

echo "Starting web server"
/usr/sbin/php-fpm
/usr/sbin/httpd -k start
echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
touch /var/log/php-fpm/www-error.log
tail -f /var/log/php-fpm/www-error.log /var/log/httpd/error_log

