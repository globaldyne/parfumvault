<?php
if (!defined('pvault_panel')){ die('Not Found');}

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 
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


$countriesJson = file_get_contents(__ROOT__.'/db/countries.json');
$pubChemApi = 'https://pubchem.ncbi.nlm.nih.gov/rest';
$pvLibraryAPI = $settings['pv_library_api_url'];

$userID = (int)$user['id'];
$role = (int)$user['role'];

?>
