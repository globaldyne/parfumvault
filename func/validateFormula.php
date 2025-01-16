<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function validateFormula($fid, $bottle, $new_conc, $mg, $defCatClass, $qStep, $isFormula = 0) {
    global $conn, $userID;
	
    // Initialize arrays to collect validation results and error messages
    $errors = array();

    // Fetch formula details
    $formula_query = mysqli_query($conn, "SELECT ingredient, quantity, concentration FROM formulas WHERE fid = '$fid' AND exclude_from_calculation = '0' AND owner_id = '$userID'");
    if (!$formula_query) {
        return array("Error fetching formula details");
    }

    // Process each ingredient in the formula
    while ($formula = mysqli_fetch_array($formula_query)) {
        $ingredient_name = $formula['ingredient'];
        $ingredient_query = mysqli_query($conn, "SELECT cas, $defCatClass FROM ingredients WHERE name = '$ingredient_name' AND owner_id = '$userID'");
        if (!$ingredient_query) {
            $errors[] = "Error fetching ingredient details for $ingredient_name";
            continue;
        }

        $ing = mysqli_fetch_array($ingredient_query);
        $cas = $ing['cas'];
		$limitIFRA = searchIFRA($cas,$ingredient_name,null,$defCatClass,0);
		$limit = $limitIFRA['val'];
		$type = $limitIFRA['type'];
		
		
        // Calculate new quantity and concentration
        $new_quantity = $formula['quantity'] / $mg * $new_conc;
        $conc = ($new_quantity / $bottle) * 100;
        $conc_p = number_format(($formula['concentration'] / 100) * $conc, $qStep);

        if ($limit) {
            if ($limit < $conc_p) {
                $errors[] = "$ingredient_name exceeds IFRA limit $limit% - $type";
				if($type === "PROHIBITION"){
					$errors[] = "$type is PROHIBITED";
				}
            }
        } else {
            if ($ing[$defCatClass] !== null) {
                if ($ing[$defCatClass] < $conc_p) {
                    $errors[] = "$ingredient_name exceeds local DB limit $ing[$defCatClass]%";
                }
            } else {
                $errors[] = "No limit record found for ingredient $ingredient_name";
            }
        }
    }

    // Return errors if any found
    if (!empty($errors)) {
        return $errors;
    }

    // Return 0 if all validations passed
    return 0;
}


?>
