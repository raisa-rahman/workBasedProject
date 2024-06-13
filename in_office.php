<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$today = date('Y-m-d');

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare the SQL statement and check for errors
$stmt = $mysqli->prepare("SELECT desk, user_id FROM bookings WHERE date = ?");
if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param('s', $today);
$stmt->execute();
$stmt->bind_result($desk, $name);

$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = ["desk" => $desk, "name" => $name];
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(to right, #007bff, #6f42c1, #28a745, #ffc107);
        }
        .navbar a {
            color: #fff !important;
        }
        .navbar .navbar-brand:hover,
        .navbar .nav-link:hover {
            color: #000 !important;
        }
        h1 {
            color: #343a40;
            margin-top: 20px;
        }
        .table thead {
            background-color: #007bff;
            color: #fff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #e9ecef;
        }
        .table tbody tr:hover {
            background-color: #cce5ff;
        }
    </style>
</head>
<body>

<!-- Header section with home and logout buttons -->
<header class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="main.php">Home</a> <!-- Link to home page -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logout</a> <!-- Link to logout page -->
            </li>
        </ul>
    </div>
</header>

<div class="container">
    <h1 class="text-center">Who Is In Office Now?</h1>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Desk</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['desk']); ?></td>
                    <td><?php echo htmlspecialchars($booking['name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
