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

$stmt = $mysqli->prepare("SELECT desk, name FROM bookings WHERE date = ?");
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
</head>
<body>

<!-- Header section with home and logout buttons -->
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

</body>
</html>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who Is In Office Now</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="text-center">Who Is In Office Now?</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Desk</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['desk']; ?></td>
                    <td><?php echo $booking['name']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
