#!/bin/sh
#
#
# Reset admin pass
# Script Version: v1.4
# Author: John Belekios <john@globaldyne.co.uk>
#
#

EMAIL=$1

if [ -z "$EMAIL" ]
then
	echo "Invalid syntax, please provide user's email"
	exit 0;
fi


ID=$(mysql -h localhost -upvault -ppvault pvault -e "SELECT id FROM users WHERE email = '$EMAIL';")
if [ -z "$ID" ]
then
        echo "A user with email $EMAIL, not found"
        exit 0;
fi

PASS=$(openssl rand -hex 8)
VER=$(cat /var/www/html/VERSION.md)

if (( $(echo "$VER > 4.7" | bc -l) )); then
        mysql -h localhost -upvault -ppvault pvault -e \
                "UPDATE users SET password = '$PASS' WHERE email = '$EMAIL';"

else
        mysql -h localhost -upvault -ppvault pvault -e \
                "UPDATE users SET password = PASSWORD('$PASS') WHERE email = '$EMAIL';"
fi
clear
echo Username: $EMAIL
echo Password: $PASS
