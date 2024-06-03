<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Retrieve user's upcoming bookings
$stmt = $mysqli->prepare("SELECT id, date, desk FROM bookings WHERE name = ? AND date >= CURDATE() ORDER BY date ASC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($booking_id, $date, $desk);

// Initialize an array to store user's upcoming bookings
$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = ["id" => $booking_id, "date" => $date, "desk" => $desk];
}

$stmt->close();

// Handle booking deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_booking'])) {
    $booking_id = $_POST['delete_booking'];
    $stmt_delete = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt_delete->bind_param('i', $booking_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: manage_bookings.php"); // Redirect to refresh the page after deletion
    exit;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
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
    <h1 class="text-center">Manage Upcoming Bookings</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Desk</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['desk']); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="delete_booking" value="<?php echo $booking['id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
