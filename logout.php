<?php
session_start();
unset($_SESSION['parfumvault']);
session_unset();
session_destroy();
header('Location: login.php');
?>
