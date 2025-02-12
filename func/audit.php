<?php
if (!defined('pvault_panel')){ die('Not Found');}

function logAuditEvent($email, $action, $result) {
    global $conn, $system_settings;

    if (!$system_settings['SYSTEM_audit']) {
        return;
    }

    $email = mysqli_real_escape_string($conn, $email);
    $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
    $browser = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);
    $timestamp = date('Y-m-d H:i:s');
    try {
        $audit_query = "INSERT INTO audit_log (email, ip, browser, timestamp, action, result) 
                        VALUES ('$email', '$ip', '$browser', '$timestamp', '$action', '$result')";

        if (!mysqli_query($conn, $audit_query)) {
            throw new Exception("Failed to insert audit log: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        error_log("PV error: " . $e->getMessage());
    }
}
?>