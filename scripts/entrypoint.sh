#!/bin/bash

echo "----------------------------------"
echo "READY - Perfumer's Vault Ver $(cat /html/VERSION.md)"
echo "Starting web server"

RETRY_INTERVAL=5 # Retry every 5 seconds
MAX_RETRIES=6    # Retry for up to 30 seconds (6 * 5 seconds)

if [ ! -d "/tmp/php" ] 
then
    echo "Temp directory not exists, creating /tmp/php." 
    mkdir -p /tmp/php
fi

if [ ! -d $TMP_PATH ] 
then
    echo "Temp directory not exists, creating $TMP_PATH." 
    mkdir -p $TMP_PATH
fi

# Function to test database connection
test_db_connection() {
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" 2>/dev/null
    return $? # Return 0 if successful, non-zero otherwise
}


# Retry loop
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    echo "Testing database connection (Attempt $((RETRY_COUNT + 1))/$MAX_RETRIES)..."
    if test_db_connection; then
        echo "Database connection successful. Starting up the app..."
        
        php-fpm
        nginx -e /tmp/error.log
        if ps aux | grep -q "[n]ginx"; then
            echo "Server is ready"
        else
            echo "Failed to start nginx."
        fi
        touch /tmp/php-fpm-www-error.

        /usr/bin/add_role_column.sh

        tail -f /tmp/php-fpm-www-error.log

        if [ $? -eq 0 ]; then
            echo "Script executed successfully."
        else
            echo "Script execution failed. Please check the logs."
        fi
        exit 0
    else
        echo "Database connection failed. Retrying in $RETRY_INTERVAL seconds..."
        sleep $RETRY_INTERVAL
        RETRY_COUNT=$((RETRY_COUNT + 1))
    fi
done

# If the loop completes without success
echo "Failed to connect to the database after $((RETRY_INTERVAL * MAX_RETRIES)) seconds. Exiting."
exit 1

