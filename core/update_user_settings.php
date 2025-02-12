<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$pref_storage = $settings['user_pref_eng'];

if (!empty($_GET['set']) && $_GET['action'] === 'save') {
    $pref_name = mysqli_real_escape_string($conn, $_GET['set']);
    $pref_tab = mysqli_real_escape_string($conn, $_GET['tableId']);
    $data = mysqli_real_escape_string($conn, serialize($_POST));

    switch ($pref_storage) {
        case 'DB':
        case '2':
            $query = "SELECT pref_name FROM user_prefs WHERE pref_name = '$pref_name' AND owner_id = '$userID' AND pref_tab = '$pref_tab'";
            $existing = mysqli_query($conn, $query);

            if (mysqli_num_rows($existing) > 0) {
                $updateQuery = "UPDATE user_prefs SET pref_data = '$data' WHERE pref_name = '$pref_name' AND owner_id = '$userID' AND pref_tab = '$pref_tab'";
                if (!mysqli_query($conn, $updateQuery)) {
                    error_log("PV error: Database update error: " . mysqli_error($conn));
                }
            } else {
                $insertQuery = "INSERT INTO user_prefs (pref_name, pref_data, pref_tab, owner_id) VALUES ('$pref_name', '$data', '$pref_tab', '$userID')";
                if (!mysqli_query($conn, $insertQuery)) {
                    error_log("PV error: Database insert error: " . mysqli_error($conn));
                }
            }
            break;

        case 'PHP_SESS':
        case '1':
        default:
            $_SESSION["user_prefs"][$pref_name] = $_POST;
            break;
    }
    return;
}

if (!empty($_GET['set']) && $_GET['action'] === 'load') {
    $pref_name = mysqli_real_escape_string($conn, $_GET['set']);
    $pref_tab = mysqli_real_escape_string($conn, $_GET['tableId']);

    switch ($pref_storage) {
        case 'DB':
        case '2':
            $query = "SELECT pref_data FROM user_prefs WHERE pref_name = '$pref_name' AND owner_id = '$userID' AND pref_tab = '$pref_tab'";
            $result = mysqli_query($conn, $query);
            
            if ($result && $row = mysqli_fetch_assoc($result)) {
                echo json_encode(unserialize($row['pref_data']));
            } else {
                //error_log("PV error: No preference data found.");
                echo json_encode([]);
            }
            break;

        case 'PHP_SESS':
        case '1':
        default:
            echo json_encode($_SESSION["user_prefs"][$pref_name] ?? []);
            break;
    }
    return;
}
