<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit; // Exit the script
}

// Check if the booking ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Invalid request. No booking ID specified."); // Terminate the script if no booking ID is specified
}

$bookingId = intval($_GET['id']); // Ensure the ID is an integer for security

// Connect to the MySQL database
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

// Check for a connection error
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error); // Terminate the script if connection fails
}

// Prepare the SQL statement to delete the booking
$stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->bind_param('i', $bookingId); // Bind the booking ID as an integer parameter

// Execute the statement and set session variables based on success or failure
if ($stmt->execute()) {
    $_SESSION['cancellation_success'] = true; // Set success flag
} else {
    $_SESSION['cancellation_success'] = false; // Set failure flag
}

// Close the statement and the database connection
$stmt->close();
$mysqli->close();

// Redirect to the admin page
header("Location: admin.php");
exit; // Exit the script
?>
