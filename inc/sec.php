<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

session_start();
if(!isset($_SESSION['parfumvault'])){
	header('Location: login.php');
	exit;
}

?>