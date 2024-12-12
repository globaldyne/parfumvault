#!/bin/bash

# Define the API endpoint
API_URL="https://vault.jbparfum.com/api.php?key=12345678&do=upload&type=formula"

# Function to generate a random 16-character unique string
generate_fid() {
    echo $(cat /dev/urandom | gtr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)
}

# Function to generate a list of ingredients for a formula
generate_ingredients() {
    local formula_fid=$1
    local num_ingredients=$2
    echo "["
    for ((j = 1; j <= num_ingredients; j++)); do
        cat <<EOL
        {
          "fid": "$formula_fid",
          "ingredient": "Ingredient $j",
          "concentration": $((RANDOM % 100 + 1)),
          "dilutant": "None",
          "quantity": $(awk -v seed=$RANDOM 'BEGIN{srand(seed); printf "%.2f", rand() * 10}')
        }
EOL
        if [[ $j -lt $num_ingredients ]]; then
            echo ","
        fi
    done
    echo "]"
}

# Function to generate a formula JSON object
generate_formula() {
    local fid=$(generate_fid)
    local index=$1
    local num_ingredients=${2:-10}
    local ingredients=$(generate_ingredients $fid $num_ingredients)
    cat <<EOL
    {
      "fid": "$fid",
      "name": "Formula $index",
      "product_name": "Product $index",
      "notes": "Notes for Formula $index",
      "concentration": $((RANDOM % 100 + 1)),
      "status": $((RANDOM % 2)),
      "created_at": "$(date +"%Y-%m-%d %H:%M:%S")",
      "isProtected": $((RANDOM % 2)),
      "rating": $((RANDOM % 5 + 1)),
      "profile": "Standard",
      "src": $((RANDOM % 5 + 1)),
      "customer_id": $((1000 + index)),
      "revision": $((RANDOM % 10 + 1)),
      "madeOn": "$(date +"%Y-%m-%d")",
      "ingredients": $ingredients
    }
EOL
}

# Number of formulas to generate (default to 3 if not provided)
NUM_FORMULAS=${1:-3}
# Number of ingredients per formula (default to 10 if not provided)
NUM_INGREDIENTS=${2:-10}

# Create a JSON file with the formulas array
{
  echo "{\"formulas\":["
  for ((i = 1; i <= NUM_FORMULAS; i++)); do
    generate_formula $i $NUM_INGREDIENTS
    if [[ $i -lt $NUM_FORMULAS ]]; then
      echo ","
    fi
  done
  echo "]}"
} > formulas.json

# Use curl to send the JSON file via POST request
response=$(curl -s -X POST -H "Content-Type: application/json" -d @formulas.json "$API_URL")

# Print the response
echo "Response from API: $response"

# Clean up the JSON file
rm -f formulas.json