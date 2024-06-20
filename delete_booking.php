<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure the booking ID is provided via GET parameter
if (!isset($_GET['id'])) {
    header("Location: main.php");
    exit;
}

$booking_id = $_GET['id'];

// Create a new mysqli object to connect to the database
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare and execute the DELETE statement
$stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");

if ($stmt === false) {
    // If the preparation of the statement fails, set deletion success to false
    $_SESSION['deletion_success'] = false;
} else {
    // Bind the booking ID parameter to the DELETE statement
    $stmt->bind_param('i', $booking_id);
    
    // Execute the statement and set the session variable based on success or failure
    if ($stmt->execute()) {
        $_SESSION['deletion_success'] = true;
    } else {
        $_SESSION['deletion_success'] = false;
    }
    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();

// Redirect back to the manage bookings page
header("Location: manage_bookings.php");
exit;
?>
