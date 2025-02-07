<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function get_formula_notes($conn, $fid, $cat) {
    global $userID;

    $categories = ['top' => 'Top', 'heart' => 'Heart', 'base' => 'Base'];
    $results = ['top' => [], 'heart' => [], 'base' => []];

    // Fetch formulas based on the provided formula ID
    $formulas = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' AND owner_id = '$userID' ORDER BY exclude_from_summary");
    if (!$formulas) {
        error_log("PV error: Failed to fetch formulas for fid $fid: " . mysqli_error($conn));
        return null;
    }

    // Process each formula ingredient
    while ($formula = mysqli_fetch_assoc($formulas)) {
        foreach ($categories as $key => $profile) {
            $ingredientQuery = "SELECT name AS ing, category 
                                FROM ingredients 
                                WHERE name = '{$formula['ingredient']}' 
                                  AND profile = '$profile' 
                                  AND category IS NOT NULL 
                                  AND owner_id = '$userID'";
            $ingredient = mysqli_fetch_assoc(mysqli_query($conn, $ingredientQuery));

            if ($ingredient) {
                $categoryQuery = "SELECT image, name 
                                  FROM ingCategory 
                                  WHERE id = '{$ingredient['category']}' 
                                    AND image IS NOT NULL 
                                    AND owner_id = '$userID'";
                $categoryData = mysqli_fetch_assoc(mysqli_query($conn, $categoryQuery));

                if ($categoryData) {
                    $results[$key][] = [
                        'name' => $categoryData['name'],
                        'image' => $categoryData['image'],
                        'ing' => $ingredient['ing']
                    ];
                }
            }
        }
    }

    return isset($results[$cat]) ? arrFilter(array_filter($results[$cat])) : null;
}

function get_formula_excludes($conn, $fid, $cat) {
    global $userID;

    $categories = ['top' => 'Top', 'heart' => 'Heart', 'base' => 'Base'];
    $excludes = [];

    if (!isset($categories[$cat])) {
        error_log("PV error: Invalid category $cat provided for exclusion.");
        return [];
    }

    // Fetch formulas marked for exclusion
    $formulasQuery = "SELECT ingredient 
                      FROM formulas 
                      WHERE fid = '$fid' 
                        AND exclude_from_summary = '1' 
                        AND owner_id = '$userID'";
    $formulas = mysqli_query($conn, $formulasQuery);

    if (!$formulas) {
        error_log("PV error: Failed to fetch formulas for exclusion for fid $fid: " . mysqli_error($conn));
        return [];
    }

    // Process each formula ingredient for exclusion
    while ($formula = mysqli_fetch_assoc($formulas)) {
        $profile = $categories[$cat];
        $ingredientQuery = "SELECT category 
                            FROM ingredients 
                            WHERE name = '{$formula['ingredient']}' 
                              AND profile = '$profile' 
                              AND category IS NOT NULL 
                              AND owner_id = '$userID'";
        $ingredient = mysqli_fetch_assoc(mysqli_query($conn, $ingredientQuery));

        if ($ingredient) {
            $categoryQuery = "SELECT name 
                              FROM ingCategory 
                              WHERE id = '{$ingredient['category']}' 
                                AND owner_id = '$userID'";
            $categoryName = mysqli_fetch_assoc(mysqli_query($conn, $categoryQuery));

            if ($categoryName) {
                $excludes[] = $categoryName['name'];
            }
        }
    }

    return array_filter($excludes);
}


?>