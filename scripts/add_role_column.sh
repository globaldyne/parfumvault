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
           
    RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "$QUERY" -s -N)
    echo "$RESULT"
}

# Function to add the required columns if they do not exist
add_columns() {
    ALTER_QUERY="ALTER TABLE \`users\` 
                 ADD \`role\` INT NOT NULL DEFAULT '1' AFTER \`email\`, 
                 ADD \`updated_at\` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL AFTER \`role\`, 
                 ADD \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER \`updated_at\`;"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$ALTER_QUERY"
    if [ $? -eq 0 ]; then
        echo "Columns 'role', 'updated_at', and 'created_at' added successfully."
    else
        echo "Failed to add columns. Please check your database permissions."
    fi
}

# Check if the 'role' column exists in the 'users' table
COLUMN_EXISTS=$(check_column_exists "role" "users")

if [ "$COLUMN_EXISTS" -eq 0 ]; then
    echo "The db schema needs to be modified. Adding the required columns..."
    add_columns
else
    echo "The db schema is updated. No changes needed."
fi
