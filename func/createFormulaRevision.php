<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once(__ROOT__.'/func/compareFormulas.php');

function createFormulaRevision($fid, $method, $conn) {
    global $userID;

    // Fetch the current formula data
    $q_a = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
    
    if (!$q_a || mysqli_num_rows($q_a) == 0) {
        return false;
    }

    // Fetch the current revision number
    $rev_query = mysqli_query($conn, "SELECT revision FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'");
    if (!$rev_query || mysqli_num_rows($rev_query) == 0) {
        return false; // No metadata found
    }
    
    $current_rev = mysqli_fetch_assoc($rev_query);
    $nr = $current_rev['revision'] + 1;

    // If revision is 0, create the initial revision
    if ($current_rev['revision'] == 0) {
        $nr = 1;
        $q_insert_initial = "INSERT INTO formulasRevisions (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, revision, revisionMethod, owner_id) 
                             SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, '1', '$method', '$userID' 
                             FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'";
        
        $q_update_initial = "UPDATE formulasMetaData SET revision = '1' WHERE fid = '$fid' AND owner_id = '$userID'";

        if (mysqli_query($conn, $q_insert_initial) && mysqli_query($conn, $q_update_initial)) {
            return true;
        }
        return false;
    }

    // Fetch the latest revision data
    $q_b = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity, notes FROM formulasRevisions WHERE fid = '$fid' AND revision = '".$current_rev['revision']."' AND owner_id = '$userID'");
    
    // Convert results into arrays
    $formula_a = [];
    while ($formula = mysqli_fetch_assoc($q_a)) {
        $formula_a[] = $formula;
    }

    $formula_rev = [];
    while ($formula = mysqli_fetch_assoc($q_b)) {
        $formula_rev[] = $formula;
    }

    // Check if revision exists and compare
    if (!empty($formula_rev) && compareFormula($formula_rev, $formula_a, ['ingredient', 'concentration', 'dilutant', 'quantity', 'notes'], null, null)) {
        $q_insert = "INSERT INTO formulasRevisions (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, revision, revisionMethod, owner_id) 
                     SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, '$nr', '$method', '$userID' 
                     FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'";
        
        $q_update = "UPDATE formulasMetaData SET revision = '$nr' WHERE fid = '$fid' AND owner_id = '$userID'";

        if (mysqli_query($conn, $q_insert) && mysqli_query($conn, $q_update)) {
            return true;
        }
    }
    
    return false;
}
?>
