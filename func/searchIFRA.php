<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function searchIFRA($cas, $name, $get, $defCatClass, $isFormula = 0) {
    global $conn;
    
    if (empty($name)) {
        return null;
    }
    
    // Build the query conditionally based on $cas value
    if ($cas !== '0') { // IGNORE VALUE FOR CARRIERS
        if ($cas) {
            $q = "cas = '$cas'";
        } else {
            $q = "name = '$name' OR synonyms LIKE '%$name%'";
        }
        if($isFormula){
			$query = "SELECT risk, $defCatClass, type FROM IFRALibrary WHERE $q AND $defCatClass REGEXP '^[0-9,]+$'";
		} else {
        	$query = "SELECT risk, $defCatClass, type FROM IFRALibrary WHERE $q";
		}
        $result = mysqli_query($conn, $query);
        $res = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if ($get) {
            return $res[$get] ?? null;  // Return the specific column requested, or null if not found
        } else {
            if ($res) {
                if (empty($res[$defCatClass])) {
                    return array(
                        'type' => $res['type'],
                        'risk' => $res['risk'],
                        'val' => null // val is null if $defCatClass is empty
                    );
                } else {
                    if (in_array($res['type'], ['PROHIBITION', 'SPECIFICATION'])) {
                        return array(
                            'risk' => $res['risk'],
                            'type' => $res['type'],
                            'val' => 0
                        );
                    } else {
                        return array(
                            'risk' => $res['risk'],
                            'type' => $res['type'],
                            'val' => (double)$res[$defCatClass]
                        );
                    }
                }
            }
        }
    }
    
    return null; // Fallback if no conditions are met
}

?>
