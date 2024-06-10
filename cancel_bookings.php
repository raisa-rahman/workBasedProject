<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'functions.php';

$user_id = $_SESSION['user_id'];

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the user is an admin
if (!isAdmin($user_id, $mysqli)) {
    die("Access denied. You do not have permission to view this page.");
}

if (!isset($_GET['id'])) {
    die("No booking ID specified.");
}

$booking_id = intval($_GET['id']);

// Prepare the SQL statement to delete the booking
$stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($mysqli->error));
}

$stmt->bind_param('i', $booking_id);
$stmt->execute();

if ($stmt->error) {
    die("Execute failed: " . htmlspecialchars($stmt->error));
}

$stmt->close();
$mysqli->close();

// Redirect back to the bookings page
header("Location: admin.php");
exit;
?>
