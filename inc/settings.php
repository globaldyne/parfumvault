<?php
if (!defined('pvault_panel')){ die('Not Found');}

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['userID']."'")); 
$pv_meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pv_meta")); 


$pubChemApi = 'https://pubchem.ncbi.nlm.nih.gov/rest';
$pvOnlineAPI = $settings['pv_online_api_url'];
?>
