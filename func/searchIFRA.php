<?php 
if (!defined('pvault_panel')){ die('Not Found');}
function searchIFRA($cas, $name, $get, $defCatClass, $isFormula = 0) {
    global $conn, $userID;

    // Validate required parameters
    if (empty($name) && empty($cas)) {
        error_log("PV error: Both 'name' and 'cas' parameters are empty in searchIFRA.");
        return null;
    }

    // Prepare the query condition based on the 'cas' value
    $q = "";
    if ($cas !== '0') { // Ignore value for carriers
        if ($cas) {
            $q = "cas = '" . mysqli_real_escape_string($conn, $cas) . "'";
        } else {
            $q = "name = '" . mysqli_real_escape_string($conn, $name) . "' OR synonyms LIKE '%" . mysqli_real_escape_string($conn, $name) . "%'";
        }
    } else {
        return null; // Return null if 'cas' is '0'
    }

    // Build the query based on the formula flag
    if ($isFormula) {
        $query = "SELECT risk, $defCatClass, type FROM IFRALibrary WHERE $q AND $defCatClass REGEXP '^[0-9,]+$' AND owner_id = '$userID'";
    } else {
        $query = "SELECT risk, $defCatClass, type FROM IFRALibrary WHERE $q AND owner_id = '$userID'";
    }

    // Execute the query
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("PV error: Failed to execute query for IFRALibrary: " . mysqli_error($conn));
        return null;
    }

    // Fetch the result
    $res = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$res) {
        return null; // Return null if no result found
    }

    // Handle the case where a specific field is requested
    if ($get) {
        return isset($res[$get]) ? $res[$get] : null;
    } else {
        // Return structured data
        if (empty($res[$defCatClass])) {
            return [
                'type' => $res['type'],
                'risk' => $res['risk'],
                'val' => null // val is null if $defCatClass is empty
            ];
        } else {
            if (in_array($res['type'], ['PROHIBITION', 'SPECIFICATION'])) {
                return [
                    'risk' => $res['risk'],
                    'type' => $res['type'],
                    'val' => 0
                ];
            } else {
                return [
                    'risk' => $res['risk'],
                    'type' => $res['type'],
                    'val' => (double)$res[$defCatClass]
                ];
            }
        }
    }

    return null; // Fallback if no conditions are met
}


?>
