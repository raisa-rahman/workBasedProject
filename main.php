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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #343a40 !important;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .container {
            margin-top: 50px;
        }
        .vertical-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .vertical-buttons .btn {
            width: 300px;
            margin-bottom: 15px;
            font-size: 1.2em;
            color: #fff;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-blue {
            background-color: #007bff;
        }
        .btn-blue:hover {
            background-color: #0056b3;
        }
        .btn-yellow {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-yellow:hover {
            background-color: #e0a800;
        }
        .btn-green {
            background-color: #28a745;
        }
        .btn-green:hover {
            background-color: #218838;
        }
        .btn-purple {
            background-color: #6f42c1;
        }
        .btn-purple:hover {
            background-color: #5a33a4;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark">
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
        <h1 class="text-center">Welcome to Integrella's Desk Booking System</h1>
        <div class="d-flex justify-content-center mt-5">
            <div class="vertical-buttons">
                <a href="create_booking.php" class="btn btn-blue">Create a Desk Booking</a>
                <a href="manage_bookings.php" class="btn btn-yellow">Manage My Bookings</a>
                <a href="in_office.php" class="btn btn-green">Who is in Office Now?</a>
                <a href="admin.php" class="btn btn-purple">Admin Page</a>
            </div>
        </div>
    </div>
</body>
</html>
