<?php
if (!defined('pvault_panel')){ die('Not Found');}

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['userID']."'")); 
$pv_meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pv_meta")); 

$countriesJson = file_get_contents(__ROOT__.'/db/countries.json');
$pubChemApi = 'https://pubchem.ncbi.nlm.nih.gov/rest';
$pvLibraryAPI = $settings['pv_library_api_url'];

$userID = (int)$user['id'];
$role = (int)$user['role'];

?>
