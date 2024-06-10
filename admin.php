<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'functions.php';  // Ensure the isAdmin function is defined in this file

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the user is an admin
if (!isAdmin($user_id, $mysqli)) {
    die("Access denied. You do not have permission to view this page.");
}

$dateFrom = $_POST['date_from'] ?? null;
$dateTo = $_POST['date_to'] ?? null;

if ($dateFrom && $dateTo) {
    $stmt = $mysqli->prepare("SELECT b.id, b.desk, b.date, u.username FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.date BETWEEN ? AND ? ORDER BY b.date ASC");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
} else {
    $stmt = $mysqli->prepare("SELECT b.id, b.desk, b.date, u.username FROM bookings b JOIN users u ON b.user_id = u.id ORDER BY b.date ASC");
}

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

// Prepare the SQL statement to select all future bookings
$stmt = $mysqli->prepare("SELECT desk, user_id, date FROM bookings WHERE date >= ?");
if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param('s', $today);
$stmt->execute();
$stmt->bind_result($desk, $name, $date);

$futureBookings = [];
while ($stmt->fetch()) {
    $futureBookings[] = ["desk" => $desk, "name" => $name, "date" => $date];
}

$stmt->close();
$mysqli->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings (Admin)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<header class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="main.php">Home</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logout</a>
            </li>
        </ul>
    </div>
</header>

<div class="container">
    <h1 class="text-center">All Bookings</h1>
    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="date_from">From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" required>
            </div>
            <div class="form-group col-md-5">
                <label for="date_to">To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" required>
            </div>
            <div class="form-group col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
            </div>
        </div>
    </form>
    <table class="table">
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['desk']); ?></td>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                    <td>
                        <a href="cancel_booking.php?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-danger">Cancel</a>
                        <a href="edit_booking.php?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($dateFrom && $dateTo): ?>
        <div class="text-center">
            <a href="download_report.php?date_from=<?php echo htmlspecialchars($dateFrom); ?>&date_to=<?php echo htmlspecialchars($dateTo); ?>" class="btn btn-success">Download Report</a>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>Desk</th>
                <th>Name</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($futureBookings as $futureBooking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($futureBooking['desk']); ?></td>
                    <td><?php echo htmlspecialchars($futureBooking['name']); ?></td>
                    <td><?php echo htmlspecialchars($futureBooking['date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
