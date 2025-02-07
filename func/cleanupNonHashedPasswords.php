<?php
if (!defined('pvault_panel')){ die('Not Found');}
/**
 * Cleans up non-hashed passwords from the users table.
 *
 * @param mysqli $conn The active MySQL connection.
 * @return bool True if at least one user was deleted, false otherwise.
 */
function cleanupNonHashedPasswords($conn) {
    try {
        $selectPasswordsQuery = "SELECT id, password FROM users";
        $passwordsStmt = $conn->prepare($selectPasswordsQuery);
        $passwordsStmt->execute();
        $passwordsResult = $passwordsStmt->get_result();

        $userDeleted = false;

        while ($row = $passwordsResult->fetch_assoc()) {
            $userId = $row['id'];
            $password = $row['password'];

            // Check if the password is hashed (example: check for bcrypt hash starting with "$2y$")
            if (!preg_match('/^\$2y\$[0-9]{2}\$[A-Za-z0-9.\/]{53}$/', $password)) {
                // Delete entry if the password is not hashed
                $deleteUserQuery = "DELETE FROM users WHERE id = ?";
                $deleteUserStmt = $conn->prepare($deleteUserQuery);
                $deleteUserStmt->bind_param('s', $userId);

                if ($deleteUserStmt->execute()) {
                    $userDeleted = true;
                }

                $deleteUserStmt->close();
            }
        }

        $passwordsStmt->close();

        // Return true if at least one user was deleted
        return $userDeleted;
    } catch (Exception $e) {
        error_log("Error during cleanup: " . $e->getMessage());
        return false;
    }
}
?>
