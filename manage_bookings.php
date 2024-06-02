<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT date, name, desk FROM bookings WHERE name = ? ORDER BY date ASC");

// Check if prepare() failed
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($mysqli->error));
}

// Bind parameters
$stmt->bind_param('i', $user_id);

// Check if bind_param() failed
if ($stmt->error) {
    die("Bind failed: " . htmlspecialchars($stmt->error));
}

// Execute the statement
$stmt->execute();

// Check if execute() failed
if ($stmt->error) {
    die("Execute failed: " . htmlspecialchars($stmt->error));
}

$stmt->bind_result($booking_id, $desk, $date);

$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = ["id" => $booking_id, "desk" => $desk, "date" => $date];
}

$stmt->close();
$mysqli->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
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

    <h1 class="text-center">My Bookings</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Desk</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['desk']); ?></td>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><a href="cancel_booking.php?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-danger">Cancel</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
