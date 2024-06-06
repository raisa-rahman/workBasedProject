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

if (!isAdmin($user_id, $mysqli)) {
    die("Access denied. You do not have permission to view this page.");
}

$dateFrom = $_GET['date_from'] ?? null;
$dateTo = $_GET['date_to'] ?? null;

if (!$dateFrom || !$dateTo) {
    die("Invalid date range.");
}

$stmt = $mysqli->prepare("SELECT b.id, b.desk, b.date, u.username FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.date BETWEEN ? AND ? ORDER BY b.date ASC");
$stmt->bind_param("ss", $dateFrom, $dateTo);

if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($mysqli->error));
}

$stmt->execute();

if ($stmt->error) {
    die("Execute failed: " . htmlspecialchars($stmt->error));
}

$stmt->bind_result($booking_id, $desk, $date, $username);

$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = ["id" => $booking_id, "desk" => $desk, "date" => $date, "username" => $username];
}

$stmt->close();

$reportFile = fopen('php://output', 'w');
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="report.csv"');

fputcsv($reportFile, ['Desk', 'Date', 'User']);
foreach ($bookings as $booking) {
    fputcsv($reportFile, $booking);
}

fclose($reportFile);
$mysqli->close();
exit;
?>
