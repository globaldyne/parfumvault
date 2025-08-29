#!/bin/sh
#
#
# Reset admin pass
# Script Version: v1.7
#
#

#!/bin/bash

EMAIL=$1

if [ -z "$EMAIL" ]; then
    echo "Invalid syntax, please provide the user's email"
    exit 0
fi

PASS=$(openssl rand -hex 8)
HASHED_PASS=$(php -r "echo password_hash('$PASS', PASSWORD_DEFAULT);")
ID=$(mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -sN -e "SELECT id FROM users WHERE email = '$EMAIL';")

if [ -z "$ID" ]; then
    mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e \
        "INSERT INTO users (email, password, fullName) VALUES ('$EMAIL', '$HASHED_PASS', 'Auto Created')"
    clear
    echo "A user with email $EMAIL was not found, so it has been created."
    echo "Username: $EMAIL"
    echo "Password: $PASS"
    exit 0
fi

VER=$(cat /html/VERSION.md)

mysql -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e \
    "UPDATE users SET password = '$HASHED_PASS' WHERE email = '$EMAIL';"

clear
echo "Username: $EMAIL"
echo "Password: $PASS"

