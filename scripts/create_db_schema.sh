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


TABLE_EXISTS=$(check_table_exists "pv_meta")

if [ "$TABLE_EXISTS" -eq 0 ]; then
    echo "Table identifier does not exist. Importing schema from /html/db/pvault.sql..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /html/db/pvault.sql 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "Schema imported successfully."

        # Insert schema and app versions
        APP_VER=$(cat /html/VERSION.md | tr -d '\n')
        DB_VER=$(cat /html/db/schema.ver | tr -d '\n')
        INSERT_META_QUERY="INSERT INTO pv_meta (schema_ver, app_ver) VALUES ('$DB_VER', '$APP_VER');"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "$INSERT_META_QUERY" 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "Database metadata initialized successfully."
        else
            echo "Failed to initialize database metadata."
        fi
    else
        echo "Failed to import schema. Please check your database permissions."
        exit 1
    fi
fi

# Check and manage user creation or update
USER_EMAIL=${USER_EMAIL:-""}
USER_NAME=${USER_NAME:-""}
USER_PASSWORD=${USER_PASSWORD:-""}

if [ -n "$USER_EMAIL" ] && [ -n "$USER_NAME" ] && [ -n "$USER_PASSWORD" ]; then
    USER_EXISTS_QUERY="SELECT id FROM users LIMIT 1;"
    USER_EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "$USER_EXISTS_QUERY" -s -N 2>/dev/null)

    # Hash the password if not already hashed
    if [[ ! "$USER_PASSWORD" =~ ^\$2[ayb]\$.{56}$ ]]; then
        USER_PASSWORD=$(php -r "echo password_hash('$USER_PASSWORD', PASSWORD_DEFAULT);")
    fi

    if [ -n "$USER_EXISTS" ]; then
        # Update existing user
        UPDATE_USER_QUERY="UPDATE users SET email='$USER_EMAIL', fullName='$USER_NAME', password='$USER_PASSWORD' WHERE id=(SELECT id FROM (SELECT id FROM users LIMIT 1) AS temp);"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "$UPDATE_USER_QUERY" 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "User information updated successfully."
        else
            echo "Failed to update user information."
        fi
    else
        # Insert new user
        INSERT_USER_QUERY="INSERT INTO users (email, fullName, password) VALUES ('$USER_EMAIL', '$USER_NAME', '$USER_PASSWORD');"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "$INSERT_USER_QUERY" 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "New user created successfully."
        else
            echo "Failed to create new user."
        fi
    fi
fi




