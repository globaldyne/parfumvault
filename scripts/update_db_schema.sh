#!/bin/bash

# Function to check if a table exists in the database
check_table_exists() {
    TABLE_NAME=$1
    
    QUERY="SELECT COUNT(*) 
           FROM INFORMATION_SCHEMA.TABLES 
           WHERE TABLE_SCHEMA='$DB_NAME' 
           AND TABLE_NAME='$TABLE_NAME';"
           
    RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "$QUERY" -s -N 2>/dev/null)
    echo "$RESULT"
}

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
                ADD \`last_login\` TIMESTAMP NULL DEFAULT NULL AFTER \`created_at\`;"

    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$ALTER_QUERY" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "users schema updated successfully."
    else
        echo "Failed to add columns. Please check your database permissions."
    fi
}

TABLE_EXISTS=$(check_table_exists "users")

if [ "$TABLE_EXISTS" -eq 0 ]; then
    echo "Table 'users' does not exist. Importing schema from /html/db/pvault.sql..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /html/db/pvault.sql 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "Schema imported successfully."
    else
        echo "Failed to import schema. Please check your database permissions."
    fi
fi

COLUMN_EXISTS=$(check_column_exists "last_login" "users")

if [ "$COLUMN_EXISTS" -eq 0 ]; then
    echo "The db schema needs to be modified. Adding the required columns..."
    add_columns
else
    echo "The db schema is updated. No changes needed."
fi


