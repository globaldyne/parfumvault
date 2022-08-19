<?php
if (!defined('pvault_panel')){ die('Not Found');}

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['userID']."'")); 
$pv_meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pv_meta")); 
$pv_online = mysqli_fetch_array(mysqli_query($conn, "SELECT enabled FROM pv_online"));

$pv_online['email'] = $user['email'];
$pv_online['password'] = $user['password'];

$pubChemApi = 'https://pubchem.ncbi.nlm.nih.gov/rest';

?>
