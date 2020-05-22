<?php
define('pvault_panel', TRUE);

session_start();
if(!isset($_SESSION['parfumvault'])){
	header('Location: /login.php');
}

?>