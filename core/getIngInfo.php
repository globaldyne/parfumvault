<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');

if (isset($_GET['id']) && isset($_GET['filter'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $filters = explode(',', mysqli_real_escape_string($conn, $_GET['filter']));

    $response = [];
    foreach ($filters as $filter) {
        if (!in_array($filter, ['solvent', 'purity'])) {
            continue;
        }

        $query = "SELECT $filter FROM ingredients WHERE id = '$id'";
        $info = mysqli_fetch_array(mysqli_query($conn, $query));

        if ($info && isset($info[$filter])) {
            switch ($filter) {
                case "solvent":
                    $response[$filter] = $info[$filter] ?: "None";
                    break;
                case "purity":
                    $response[$filter] = (float)$info[$filter] ?: 100;
                    break;
            }
        } else {
            $response[$filter] = "No data";
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    return;
}

?>