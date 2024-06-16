<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include necessary functions
require 'functions.php';

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the user is an admin
if (!isAdmin($user_id, $mysqli)) {
    die("Access denied. You do not have permission to view this page.");
}

// Retrieve date range from GET parameters
$dateFrom = $_GET['date_from'] ?? null;
$dateTo = $_GET['date_to'] ?? null;

// Validate date range
if (!$dateFrom || !$dateTo) {
    die("Invalid date range.");
}

// Sanitize date input
$dateFrom = $mysqli->real_escape_string($dateFrom);
$dateTo = $mysqli->real_escape_string($dateTo);

// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT id, desk, date, user_id FROM bookings WHERE date BETWEEN ? AND ? ORDER BY date ASC");

// Check if the statement was prepared correctly
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($mysqli->error));
}

// Bind parameters
$stmt->bind_param("ss", $dateFrom, $dateTo);

// Execute the statement
$stmt->execute();

// Check if execution was successful
if ($stmt->error) {
    die("Execute failed: " . htmlspecialchars($stmt->error));
}

// Bind the result variables
$stmt->bind_result($booking_id, $desk, $date, $user_id);

// Fetch the results into an array
$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = ["desk" => $desk, "date" => $date, "user_id" => $user_id];
}

// Close the statement
$stmt->close();

// Set headers for CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="Desk_Booking_Report.csv"');

// Open output stream
$reportFile = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($reportFile, ['Desk', 'Date', 'User']);

// Write each booking to the CSV file
foreach ($bookings as $booking) {
    fputcsv($reportFile, $booking);
}

// Close the file pointer
fclose($reportFile);

// Close the database connection
$mysqli->close();
exit;
?>
