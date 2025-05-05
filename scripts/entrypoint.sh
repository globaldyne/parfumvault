#!/bin/bash

echo "----------------------------------"
echo "Starting Perfumers Vault Ver $(cat /html/VERSION.md)"

# Configuration
RETRY_INTERVAL=5 # Retry every 5 seconds
MAX_RETRIES=6    # Retry for up to 30 seconds (6 * 5 seconds)

# Ensure required directories exist
if [ ! -d "/tmp/php" ]; then
    echo "Temp directory not exists, creating /tmp/php."
    mkdir -p /tmp/php
fi

if [ ! -d "$TMP_PATH" ]; then
    echo "Temp directory not exists, creating $TMP_PATH."
    mkdir -p "$TMP_PATH"
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

        # Start PHP-FPM
        php-fpm
        if [ $? -ne 0 ]; then
            echo "Failed to start php-fpm. Exiting."
            exit 1
        fi

        # Start Nginx
        nginx -e /tmp/error.log
        if ! ps aux | grep -q "[n]ginx"; then
            echo "Failed to start nginx. Exiting."
            exit 1
        fi
        echo "Server is ready."

        # Set error log file path from environment variable or use default
        ERROR_LOG="${ERROR_LOG:-/tmp/php-fpm-www-error.log}"

        # Create error log file if missing
        if [ ! -f "$ERROR_LOG" ]; then
            echo "Creating error log file at $ERROR_LOG."
            touch "$ERROR_LOG"
        fi

        # Execute the create_db_schema.sh script
        /usr/bin/create_db_schema.sh
        if [ $? -eq 0 ]; then
            echo "Database schema is up to date."
        else
            echo "create_db_schema.sh script failed. Please check the logs."
        fi

        # Execute the update_db_schema.sh script
        /usr/bin/update_db_schema.sh
        if [ $? -eq 0 ]; then
            echo "The users schema is up to date. No changes needed."
        else
            echo "update_db_schema.sh script failed. Please check the logs."
        fi

        # Start the session monitor if SESSION_MONITOR is true
        if [ "$SESSION_MONITOR" = "true" ]; then
            if [ -f "/usr/bin/session_monitor" ]; then
                /usr/bin/session_monitor &
                if [ $? -ne 0 ]; then
                    echo "Failed to start session monitor. Exiting."
                    exit 1
                fi
            else
                echo "Session monitor not installed. Continuing..."
            fi
        else
            echo "Session monitor is disabled."
        fi
        
        echo "----------------------------------"

        # Tail error logs
        echo "Tailing error logs from $ERROR_LOG..."
        tail -f "$ERROR_LOG"
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

