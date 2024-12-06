<?php 
if (!defined('pvault_panel')){ die('Not Found');}
function get_formula_notes($conn, $fid, $cat) {
    $formulas = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' ORDER BY exclude_from_summary");
    $categories = ['top' => 'Top', 'heart' => 'Heart', 'base' => 'Base'];
    $results = ['top' => [], 'heart' => [], 'base' => []];

    while ($formula = mysqli_fetch_array($formulas)) {
        foreach ($categories as $key => $profile) {
            $ingredient = mysqli_fetch_array(
                mysqli_query($conn, "SELECT name AS ing, category FROM ingredients 
                                     WHERE name = '{$formula['ingredient']}' AND profile = '$profile' AND category IS NOT NULL")
            );

            if ($ingredient) {
                $categoryData = mysqli_fetch_array(
                    mysqli_query($conn, "SELECT image, name FROM ingCategory 
                                         WHERE id = '{$ingredient['category']}' AND image IS NOT NULL")
                );

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
    $formulas = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' AND exclude_from_summary = '1'");
    $categories = ['top' => 'Top', 'heart' => 'Heart', 'base' => 'Base'];
    $excludes = [];

    while ($formula = mysqli_fetch_array($formulas)) {
        if (isset($categories[$cat])) {
            $profile = $categories[$cat];
            $category = mysqli_fetch_array(
                mysqli_query($conn, "SELECT category FROM ingredients 
                                     WHERE name = '{$formula['ingredient']}' AND profile = '$profile' AND category IS NOT NULL")
            );

            if ($category) {
                $categoryName = mysqli_fetch_array(
                    mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '{$category['category']}'")
                );

                if ($categoryName) {
                    $excludes[] = $categoryName['name'];
                }
            }
        }
    }

    return array_filter($excludes);
}


?>