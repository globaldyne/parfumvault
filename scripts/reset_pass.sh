#!/bin/sh
#
#
# Reset admin pass
# Script Version: v1.6
# Author: John Belekios <john@globaldyne.co.uk>
#
#

EMAIL=$1

if [ -z "$EMAIL" ]
then
	echo "Invalid syntax, please provide user's email"
	exit 0;
fi

PASS=$(openssl rand -hex 8)
ID=$(mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e "SELECT id FROM users WHERE email = '$EMAIL';")
if [ -z "$ID" ]
then
		mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e \
		       "INSERT INTO users (email,password,fullName) VALUES ('$EMAIL', PASSWORD('$PASS'),'Auto Created')"
		clear
		echo "A user with email $EMAIL, not found so its been created"
		echo Username: $EMAIL
		echo Password: $PASS
        exit 0;
fi


VER=$(cat /html/VERSION.md)

mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e \
       "UPDATE users SET password = PASSWORD('$PASS') WHERE email = '$EMAIL';"

clear
echo Username: $EMAIL
echo Password: $PASS
