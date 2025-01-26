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
                ADD \`isActive\` INT NOT NULL DEFAULT '1' AFTER \`role\`,
                ADD \`country\` VARCHAR(255) NULL DEFAULT NULL AFTER \`isActive\`,
                ADD \`isAPIActive\` INT NOT NULL DEFAULT '0' AFTER \`country\`,
                ADD \`API_key\` VARCHAR(255) NULL DEFAULT NULL AFTER \`isAPIActive\`, 
                ADD \`isVerified\` INT NOT NULL AFTER \`API_key\`,
                ADD \`token\` VARCHAR(255) NULL AFTER \`isVerified\`, 
                ADD \`provider\` INT NOT NULL DEFAULT '1' COMMENT '1=Local,2=SSO' AFTER \`fullName\`;"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$ALTER_QUERY" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "Columns 'isActive' and 'provider' added successfully."
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

COLUMN_EXISTS=$(check_column_exists "isActive" "users")

if [ "$COLUMN_EXISTS" -eq 0 ]; then
    echo "The db schema needs to be modified. Adding the required columns..."
    add_columns
else
    echo "The db schema is updated. No changes needed."
fi

USER_SETTINGS_TABLE_EXISTS=$(check_table_exists "user_settings")

if [ "$USER_SETTINGS_TABLE_EXISTS" -eq 0 ]; then
    echo "Table 'user_settings' does not exist. Creating table..."
    CREATE_TABLE_QUERY="CREATE TABLE \`user_settings\` (
        \`id\` INT(11) NOT NULL AUTO_INCREMENT,
        \`key_name\` VARCHAR(255) NOT NULL,
        \`value\` LONGTEXT NOT NULL,
        \`owner_id\` INT NOT NULL,
        \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        \`updated_at\` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (\`id\`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$CREATE_TABLE_QUERY" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "Table 'user_settings' created successfully."
    else
        echo "Failed to create table 'user_settings'. Please check your database permissions."
    fi
fi

SYSTEM_SETTINGS_TABLE_EXISTS=$(check_table_exists "system_settings")

if [ "$SYSTEM_SETTINGS_TABLE_EXISTS" -eq 0 ]; then
    echo "Table 'system_settings' does not exist. Creating table..."
    CREATE_TABLE_QUERY="CREATE TABLE \`system_settings\` (
        \`id\` INT(11) NOT NULL AUTO_INCREMENT,
        \`key_name\` VARCHAR(255) NOT NULL,
        \`value\` LONGTEXT NOT NULL,
        \`slug\` VARCHAR(255) NOT NULL,
        \`type\` VARCHAR(255) NOT NULL,
        \`description\` VARCHAR(255) NOT NULL,
        \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        \`updated_at\` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (\`id\`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$CREATE_TABLE_QUERY" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "Table 'system_settings' created successfully."
        INSERT_QUERY="INSERT INTO \`system_settings\` (\`key_name\`, \`value\`, \`slug\`, \`type\`, \`description\`) VALUES
        ('SYSTEM_chkVersion', '1', 'Check for updates', 'checkbox', 'Check for updates'),
        ('SYSTEM_pubChem', '1', 'Enable PubChem', 'checkbox', 'Enable or disable pubChem integration'),
        ('SYSTEM_server_url', '', 'Server URL', 'text', 'This is your Perfumers Vault installation server URL.'),
        ('INTEGRATIONS_enable', '0', 'Enable integrations', 'checkbox', 'Enable or disable integrations'),
        ('USER_selfRegister', '0', 'Enable user registration', 'checkbox', 'Enable or disable user self registration'),
        ('USER_terms_url', 'https://www.perfumersvault.com/terms-of-service', 'Terms and Conditions', 'text', 'Point this to your web site that hosts the terms and conditions info for users'),
        ('LIBRARY_enable', '1', 'Enable PV Library', 'checkbox', 'Enable or disable PV Library'),
        ('LIBRARY_apiurl', 'https://library.perfumersvault.com/api-data/api.php', 'Library API URL', 'text', 'Library API URL'),
        ('announcements', '', 'Announcement', 'textarea', 'Add here any announcement for your users when login'),
        ('EMAIL_isEnabled', '0', 'Enable email', 'checkbox', 'Enable or disable email functions, like user welcome email when register, password reset, email confirmation etc'),
        ('EMAIL_smtp_host', '', 'SMTP Host', 'text', 'This is your smtp email server ip or hostname'),
        ('EMAIL_smtp_port', '', 'SMTP Port', 'text', 'Optional, Defaults to 25'),
        ('EMAIL_from', '', 'From', 'text', 'This is the From address'),
        ('EMAIL_from_display_name', 'Perfumers Vault', 'From display name', 'text', 'A user-friendly name for the \'From\' address (optional).'),
        ('EMAIL_smtp_user', '', 'Username', 'text', 'Optional field, use only if your email server requires authentication'),
        ('EMAIL_smtp_pass', '', 'Password', 'password', 'Optional field, use only if your email server requires authentication'),
        ('EMAIL_smtp_secure', '0', 'Enable SSL', 'checkbox', 'Enable secure connection if your server supports it');"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$INSERT_QUERY" 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "Default settings inserted into 'system_settings' table successfully."
        else
            echo "Failed to insert default settings into 'system_settings' table. Please check your database permissions."
        fi
    else
        echo "Failed to create table 'system_settings'. Please check your database permissions."
    fi
fi
