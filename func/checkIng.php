<?php

function checkIng($ingredient, $defCatClass, $conn) {
    global $userID;

    // Escape inputs to prevent SQL injection
    $ingredient = mysqli_real_escape_string($conn, $ingredient);
    $userID = mysqli_real_escape_string($conn, $userID);

    // Query to check for the ingredient in the database
    $chkQuery = "SELECT id, name, $defCatClass, profile, cas 
                 FROM ingredients 
                 WHERE (name = '$ingredient' OR chemical_name = '$ingredient') AND owner_id = '$userID'";
    $chkResult = mysqli_query($conn, $chkQuery);

    if ($chkResult && mysqli_num_rows($chkResult) > 0) {
        while ($qValues = mysqli_fetch_assoc($chkResult)) {
            $casQ = "";
            if (!empty($qValues['cas'])) {
                $casQ = "OR cas LIKE '%" . mysqli_real_escape_string($conn, $qValues['cas']) . "%'";
            }

            // Query to check IFRALibrary for the ingredient
            $ifraQuery = "
                SELECT name, $defCatClass 
                FROM IFRALibrary 
                WHERE (name = '" . mysqli_real_escape_string($conn, $qValues['name']) . "' 
                       OR synonyms LIKE '%" . mysqli_real_escape_string($conn, $qValues['name']) . "%' $casQ) 
                      AND owner_id = '$userID'";
            $chkIFRA = mysqli_fetch_assoc(mysqli_query($conn, $ifraQuery));

            // Query to check suppliers for pricing data
            $priceQuery = "SELECT price 
                           FROM suppliers 
                           WHERE ingID = '" . mysqli_real_escape_string($conn, $qValues['id']) . "' 
                           AND owner_id = '$userID'";
            $chkPrice = mysqli_fetch_assoc(mysqli_query($conn, $priceQuery));

            // Validate the data
            if (empty($chkIFRA[$defCatClass]) && empty($qValues[$defCatClass])) {
                return ['text' => 'Missing usage data', 'code' => 1];
            }
            if (empty($chkPrice['price'])) {
                return ['text' => 'Missing pricing data', 'code' => 2];
            }
            if (empty($qValues['profile'])) {
                return ['text' => 'Missing profile data', 'code' => 3];
            }
        }
    } else {
        return ['text' => 'Ingredient is missing from the database', 'code' => 4];
    }

    return;
}


