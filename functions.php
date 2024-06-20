<?php
/**
 * Check if a user is an administrator.
 *
 * @param int $user_id The ID of the user to check.
 * @param mysqli $mysqli The MySQLi connection object.
 * @return bool Returns true if the user is an administrator, false otherwise.
 */
function isAdmin($user_id, $mysqli) {
    // Prepare a SQL statement to select the is_admin column from the users table where the user id matches the given user_id
    $stmt = $mysqli->prepare("SELECT is_admin FROM users WHERE id = ?");
    
    // Bind the user_id parameter to the prepared statement as an integer
    $stmt->bind_param('i', $user_id);
    
    // Execute the prepared statement
    $stmt->execute();
    
    // Bind the result of the query to the $is_admin variable
    $stmt->bind_result($is_admin);
    
    // Fetch the result of the query
    $stmt->fetch();
    
    // Close the statement
    $stmt->close();

    // Return the value of is_admin (true if the user is an admin, false otherwise)
    return $is_admin;
}
?>
