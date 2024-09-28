<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Start the session only if not already started
}

if(!isset( $_SESSION['parfumvault']) || $_SESSION['parfumvault'] == false) {
    //expired
    echo "-1";
    session_destroy();
} else {
    //not expired
    echo "1";
}
?>
