#!/bin/bash

# Function to check if a column exists in a table
check_column_exists() {
    COLUMN_NAME=$1
    TABLE_NAME=$2
    
    QUERY="SELECT COUNT(*) 
           FROM INFORMATION_SCHEMA.COLUMNS 
           WHERE TABLE_SCHEMA='$DB_NAME' 
           AND TABLE_NAME='$TABLE_NAME' 
           AND COLUMN_NAME='$COLUMN_NAME';"
           
    RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "$QUERY" -s -N 2>/dev/null)
    echo "$RESULT"
}

# Function to add the required columns if they do not exist
add_columns() {
    ALTER_QUERY="ALTER TABLE \`users\` 
                ADD \`last_login\` TIMESTAMP NULL DEFAULT NULL AFTER \`created_at\`,
                ADD \`receiveEmails\` INT DEFAULT 1 AFTER \`last_login\`;"

    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$ALTER_QUERY" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "users schema updated successfully."
    else
        echo "Failed to add columns. Please check your database permissions."
    fi
}


COLUMN_EXISTS=$(check_column_exists "last_login" "users")

if [ "$COLUMN_EXISTS" -eq 0 ]; then
    echo "The db schema needs to be modified. Adding the required columns..."
    add_columns
else
    echo "The db schema is up to date. No changes needed."
fi


