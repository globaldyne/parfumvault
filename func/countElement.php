<?php
if (!defined('pvault_panel')){ die('Not Found');}

function countElement($element = "formulas", $filters = "") {
    global $userID, $conn;
 
    // Sanitize inputs for safety
    $element = mysqli_real_escape_string($conn, $element);
    

    // Default condition to filter by owner_id
    $defaultCondition = "owner_id = '$userID'";

    // Combine default condition with optional filters
    $conditions = $defaultCondition;
    if (!empty($filters)) {
        $conditions .= " AND $filters";
    }

    // Construct the SQL query
    $query = "SELECT COUNT(*) AS count FROM $element WHERE $conditions";

    // Execute the query and fetch the result
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("PV error: Failed to count elements in $element with filters [$filters]: " . mysqli_error($conn));
        return 0;
    }

    $data = mysqli_fetch_assoc($result);
    return (int) $data['count']; // Return the count as an integer
}
    
    
?>
