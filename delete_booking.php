<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid request. No booking ID specified.");
}

$bookingId = intval($_GET['id']); // Ensure the ID is an integer

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->bind_param('i', $bookingId);

if ($stmt->execute()) {
    $_SESSION['cancellation_success'] = true;
} else {
    $_SESSION['cancellation_success'] = false;
}

$stmt->close();
$mysqli->close();

// Redirect to admin page
header("Location:manage_bookings.php");
exit;
?>