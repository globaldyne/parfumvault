#!/bin/bash

# Configuration variables
SQL_FILE="/html/db/pvault.sql"

# Temporary files
CURRENT_SCHEMA="/tmp/current_schema.sql"
DIFF_FILE="/tmp/schema_diff.sql"

# Function to export the current database schema
export_current_schema() {
    echo "Exporting current database schema..."
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" --no-data "$DB_NAME" > "$CURRENT_SCHEMA"
    if [ $? -ne 0 ]; then
        echo "Error exporting current schema. Please check your database connection."
        exit 1
    fi
    echo "Current schema exported to $CURRENT_SCHEMA."
}

# Function to compare schemas and generate diff
generate_diff() {
    echo "Comparing schemas..."
    diff -u "$CURRENT_SCHEMA" "$SQL_FILE" > "$DIFF_FILE"
    if [ $? -eq 0 ]; then
        echo "No differences found between schemas."
        return 1
    else
        echo "Differences found. Diff saved to $DIFF_FILE."
        return 0
    fi
}

# Function to update the database schema
apply_updates() {
    echo "Applying updates to the database..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$SQL_FILE"
    if [ $? -ne 0 ]; then
        echo "Error applying updates. Please check your SQL file."
        exit 1
    fi
    echo "Database schema updated successfully."
}

# Main script logic
if [ ! -f "$SQL_FILE" ]; then
    echo "SQL file $SQL_FILE not found. Please provide a valid file."
    exit 1
fi

export_current_schema

if generate_diff; then
    echo "Updating database schema..."
    apply_updates
else
    echo "Database schema is already up to date. No changes applied."
fi

# Cleanup temporary files
rm -f "$CURRENT_SCHEMA" "$DIFF_FILE"