<?php
session_start();
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

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare and execute the DELETE statement
$stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
if ($stmt === false) {
    $_SESSION['deletion_success'] = false;
} else {
    $stmt->bind_param('i', $booking_id);
    if ($stmt->execute()) {
        $_SESSION['deletion_success'] = true;
    } else {
        $_SESSION['deletion_success'] = false;
    }
    $stmt->close();
}

$mysqli->close();

// Redirect back to main page
header("Location: manage_bookings.php");
exit;
?>
