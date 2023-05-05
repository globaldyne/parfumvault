<?php
session_start();
if(!isset( $_SESSION['parfumvault']) || $_SESSION['parfumvault'] == false) {
    //expired
    echo "-1";
    session_destroy();
} else {
    //not expired
    echo "1";
}
?>
