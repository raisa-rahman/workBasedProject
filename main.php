<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<header class="navbar navbar-expand-lg navbar-light bg-light">
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
    <h1 class="text-center">Welcome to the Integrella's Desk Booking System</h1>
    <div class="text-center">
    <a href="create_booking.php" class="btn btn-primary" style="background-color: blue; border-color: blue;">Create a Desk Booking</a>
<a href="manage_bookings.php" class="btn btn-secondary" style="background-color: yellow; border-color: yellow; color: black;">Manage My Bookings</a>
<a href="in_office.php" class="btn btn-info" style="background-color: green; border-color: green;">Who is in Office Now?</a>
<a href="admin.php" class="btn btn-primary" style="background-color: purple; border-color: purple;">Admin Page</a>


    </div>
</div>
</body>
</html>
