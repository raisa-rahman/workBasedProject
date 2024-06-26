<?php
session_start(); // Start the PHP session to access session variables

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID from session
$today = date('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

// Connect to MySQL database
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare SQL statement to retrieve bookings for the current user from today onwards
$stmt = $mysqli->prepare("SELECT date, desk, id FROM bookings WHERE user_id = ? AND date >= ?");
if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param('ss', $user_id, $today); // Bind parameters for user ID and today's date
$stmt->execute(); // Execute the prepared statement
$stmt->bind_result($date, $desk, $id); // Bind variables to prepared statement for result

$bookings = []; // Initialize an empty array to store bookings
while ($stmt->fetch()) {
    $bookings[] = ["date" => $date, "desk" => $desk, "id" => $id]; // Store each booking in the array
}

$stmt->close(); // Close the prepared statement
$mysqli->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        header {
            background-color: #007bff; /* Blue */
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        h1 {
            color: #6f42c1; /* Purple */
        }
        .btn-danger {
            background-color: #dc3545; /* Bootstrap Red */
            border-color: #dc3545;
        }
        .table thead {
            background-color: #28a745; /* Green */
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #ffeb3b; /* Yellow */
        }
    </style>
</head>
<body>

<!-- Header section with home and logout buttons -->
<header class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="main.php">Home</a> <!-- Link to home page -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a> <!-- Link to logout page -->
            </li>
        </ul>
    </div>
</header>

<div class="container my-5">
    <h1 class="text-center">Your Upcoming Desk Bookings</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Booking ID</th>    
                <th>Date</th>
                <th>Desk</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['desk']); ?></td>
                    <td>
                        <a href="delete_booking.php?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    <?php if (isset($_SESSION['deletion_success'])): ?>
        $(document).ready(function() {
            alert('<?php echo $_SESSION['deletion_success'] ? "Deletion successful." : "Error deleting booking."; ?>'); // Display deletion success or failure message
            <?php unset($_SESSION['deletion_success']); ?> // Clear deletion success message from session
        });
    <?php endif; ?>
</script>

</body>
</html>
