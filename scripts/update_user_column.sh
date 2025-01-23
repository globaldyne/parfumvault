#!/bin/bash


# Function to check if a table exists in the database
check_table_exists() {
    TABLE_NAME=$1
    
    QUERY="SELECT COUNT(*) 
           FROM INFORMATION_SCHEMA.TABLES 
           WHERE TABLE_SCHEMA='$DB_NAME' 
           AND TABLE_NAME='$TABLE_NAME';"
           
    RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "$QUERY" -s -N)
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
           
    RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "$QUERY" -s -N)
    echo "$RESULT"
}

# Function to add the required columns if they do not exist
add_columns() {
    ALTER_QUERY="ALTER TABLE \`users\` 
                ADD \`isActive\` INT NOT NULL DEFAULT '1' AFTER \`role\`,
                ADD \`country\` VARCHAR(255) NULL DEFAULT NULL AFTER \`isActive\`,
                ADD \`isAPIActive\` INT NOT NULL DEFAULT '0' AFTER \`country\`,
                ADD \`API_key\` VARCHAR(255) NULL DEFAULT NULL AFTER \`isAPIActive\`, 
                ADD \`provider\` INT NOT NULL DEFAULT '1' COMMENT '1=Local,2=SSO' AFTER \`fullName\`;"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$ALTER_QUERY"
    if [ $? -eq 0 ]; then
        echo "Columns 'isActive' and 'provider' added successfully."
    else
        echo "Failed to add columns. Please check your database permissions."
    fi
}

TABLE_EXISTS=$(check_table_exists "users")

if [ "$TABLE_EXISTS" -eq 0 ]; then
    echo "Table 'users' does not exist. Importing schema from /html/db/pvault.sql..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /html/db/pvault.sql
    if [ $? -eq 0 ]; then
        echo "Schema imported successfully."
    else
        echo "Failed to import schema. Please check your database permissions."
    fi
fi

COLUMN_EXISTS=$(check_column_exists "isActive" "users")

if [ "$COLUMN_EXISTS" -eq 0 ]; then
    echo "The db schema needs to be modified. Adding the required columns..."
    add_columns
else
    echo "The db schema is updated. No changes needed."
fi
