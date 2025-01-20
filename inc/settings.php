<?php
if (!defined('pvault_panel')){ die('Not Found');}

$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['userID']."'")); 
$pv_meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pv_meta")); 

$system_settings = [];
$system_query = "SELECT * FROM system_settings";
if ($system_result = mysqli_query($conn, $system_query)) {
    while ($system_row = mysqli_fetch_assoc($system_result)) {
        $system_settings[$system_row['key_name']] = $system_row['value'];
    }
    mysqli_free_result($system_result);
}

$user_settings = [];
$settings = [];
$query = "SELECT * FROM user_settings WHERE owner_id = '".$_SESSION['userID']."'";
if ($result = mysqli_query($conn, $query)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $user_settings[$row['key_name']] = $row['value'];
        $settings[$row['key_name']] = $row['value'];

    }
    mysqli_free_result($result);
}

$countriesJson = file_get_contents(__ROOT__.'/db/countries.json');
$pubChemApi = 'https://pubchem.ncbi.nlm.nih.gov/rest';
$pvLibraryAPI = $system_settings['LIBRARY_apiurl'];

$userID = (int)$user['id'];
$role = (int)$user['role'];

?>
