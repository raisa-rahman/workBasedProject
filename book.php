<?php
// Start a new session or resume the existing session
session_start();

// If the user is not logged in, redirect them to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if 'date' is set in the GET request and assign it to $date, or set a default date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Validate the date format to ensure it's 'Y-m-d'
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    die("Invalid date format");
}

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    // Sanitize POST data to prevent XSS
    $name = htmlspecialchars($_POST['name']);
    $desk = htmlspecialchars($_POST['desk']);

    // Create a new MySQLi connection
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

    // Check for connection errors
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Prepare an SQL statement for inserting a new booking
    $stmt = $mysqli->prepare("INSERT INTO bookings (user_id, date, desk) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameters to the SQL statement
    $stmt->bind_param('sss', $name, $date, $desk);

    // Execute the SQL statement and set a success or failure message
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Booking Successful</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Booking Failed: " . $stmt->error . "</div>";
    }

    // Close the statement and the MySQLi connection
    $stmt->close();
    $mysqli->close();
}

// Define an array of desk options
$desk_options = array(
    "Desk 1", "Desk 2", "Desk 3", "Desk 4", "Desk 5",
    "Desk 6", "Desk 7", "Desk 8", "Desk 9", "Desk 10", "Desk 11"
);

// Function to check if a desk is already booked for a given date
function checkIfDeskBooked($desk, $date) {
    // Create a new MySQLi connection
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

    // Check for connection errors
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Prepare an SQL statement for checking desk bookings
    $stmt = $mysqli->prepare("SELECT user_id FROM bookings WHERE desk = ? AND date = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameters to the SQL statement
    $stmt->bind_param('ss', $desk, $date);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();

    // Close the statement and the MySQLi connection
    $stmt->close();
    $mysqli->close();

    // Return the name if the desk is booked, null otherwise
    return $name ? $name : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .navbar-custom {
            background-color: #007bff; /* Blue */
            color: white;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .btn-booked {
            background-color: #dc3545; /* Red */
            color: white;
        }
        .btn-success {
            background-color: #28a745; /* Green */
            color: white;
        }
        .btn-primary {
            background-color: #6f42c1; /* Purple */
            color: white;
        }
        .desk-layout {
            position: relative;
            margin-top: 20px;
            width: 100%; /* Adjust width as needed */
            max-width: 1000px; /* Set a maximum width for larger images */
        }
        .desk-layout img {
            width: 100%; /* Make the image fill the container */
            height: auto; /* Maintain aspect ratio */
        }
        .desk-item {
            position: absolute;
        }
        .modal-header {
            background-color: #ffc107; /* Yellow */
            color: #343a40;
        }
        .modal-title {
            color: #343a40;
        }
        .alert-success {
            background-color: #28a745; /* Green */
            color: white;
        }
        .alert-danger {
            background-color: #dc3545; /* Red */
            color: white;
        }
    </style>
</head>

<body>
<header class="navbar navbar-expand-lg navbar-light navbar-custom">
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
    <h1 class="text-center mt-4">Book for Date: <?php echo date('F d, Y', strtotime($date)); ?></h1>
    <hr>
    <?php if (isset($msg)) { echo $msg; } ?>
    <div class="desk-layout">
        <img src="floorplan.jpeg" alt="Office Floorplan" class="img-fluid">
        <?php 
        // Define the positions of the desks on the floor plan
        $desk_positions = [
            "Desk 1" => "top: 183px; left: 232px;",
            "Desk 2" => "top: 280px; left: 232px;",
            "Desk 3" => "top: 395px; left: 232px;",
            "Desk 4" => "top: 170px; left: 360px;",
            "Desk 5" => "top: 290px; left: 360px;",
            "Desk 6" => "top: 425px; left: 360px;",
            "Desk 7" => "top: 160px; left: 625px;",
            "Desk 8" => "top: 290px; left: 625px;",
            "Desk 9" => "top: 425px; left: 625px;",
            "Desk 10" => "top: 265px; left: 740px;",
            "Desk 11" => "top: 405px; left: 740px;"
        ];
        
        // Loop through each desk option
        foreach ($desk_options as $option): 
        ?>
            <div class="desk-item" style="<?php echo $desk_positions[$option]; ?>">
                <?php
                    // Determine the button class based on booking status
                    $btnClass = "btn-success"; // Default button class
                    $bookedName = checkIfDeskBooked($option, $date);
                    if ($bookedName) {
                        $btnClass = "btn-booked";
                    }
                ?>
                <div class="btn <?php echo $btnClass; ?> book" data-desk="<?php echo $option; ?>" data-booked="<?php echo $bookedName; ?>" style="border-radius: 50%; width: 30px; height: 30px;"></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Booking: <span id="slot"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="desk">Desk</label>
                            <input required type="text" readonly name="desk" id="desk" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <!-- Use PHP to echo the username from the session -->
                            <input required type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Show tooltip on hover indicating booking status
        $(".book").hover(function() {
            var booked = $(this).attr('data-booked');
            if (booked) {
                $(this).attr('title', 'Booked by: ' + booked);
            } else {
                $(this).attr('title', 'Available');
            }
        });

        // Open the booking modal if the desk is available
        $(".book").click(function(){
            var desk = $(this).attr('data-desk');
            var booked = $(this).attr('data-booked');
            if (!booked) {
                $("#slot").html(desk);
                $("#desk").val(desk);
                $("#myModal").modal("show");
            }
        });
    });
</script>
</body>
</html>
