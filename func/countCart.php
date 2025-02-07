<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function countCart() {
    global $conn;
    global $userID;

    // Query the cart count
    $query = "SELECT COUNT(id) AS cart_count FROM cart WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        // Log the error for debugging
        error_log("PV error: Failed to count cart items: " . mysqli_error($conn));
        return 0; // Return 0 as the default count if an error occurs
    }

    $data = mysqli_fetch_assoc($result);
    return (int) $data['cart_count']; // Return the count as an integer
}


?>