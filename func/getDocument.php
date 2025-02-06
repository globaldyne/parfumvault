<?php if (!defined('pvault_panel')){ die('Not Found');}

function getDocument($ownerID, $type, $conn) {
    global $userID;

    $query = "SELECT id, name, docData 
              FROM documents 
              WHERE ownerID = '$ownerID' AND type = '$type' AND owner_id = '$userID'";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?: null;
}
?>
