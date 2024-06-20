<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Function to build the calendar for a given month, year, and room
function build_calendar($month, $year, $room) {
    // Connect to the database
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

    // If no specific room is selected, get the first room
    if ($room == 0) {
        $room = $mysqli->query("SELECT id FROM rooms LIMIT 1")->fetch_assoc()['id'];
    }

    // Prepare SQL statement to fetch bookings for the given month, year, and room
    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE MONTH(date) = ? AND YEAR(date) = ? AND ROOM_ID = ?");
    $stmt->bind_param('iii', $month, $year, $room);

    $bookings = array();
    // Execute the statement and fetch the results
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row['date'];
            }
        }
        $stmt->close();
    }
    $mysqli->close();

    // Define days of the week
    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

    // Get the first day of the month
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    // Get the number of days in the month
    $numberDays = date('t', $firstDayOfMonth);
    // Get various date components of the first day of the month
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date('Y-m-d');

    // Determine the previous and next months
    $prev_month = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
    $prev_year = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
    $next_month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
    $next_year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

    // Start building the calendar HTML
    $calendar = "<div class='calendar-container'><center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."&room=".$room."'>Previous Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."&room=".$room."'>Current Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year."&room=".$room."'>Next Month</a></center>";
    
    $calendar .= "<table class='table table-bordered'>";
    $calendar .= "<tr>";

    // Create the calendar headers
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";
    
    // Add blank cells for days of the week before the first day of the month
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td></td>";
        }
    }

    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    // Loop through all days of the month and generate the calendar cells
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $today = $date == date('Y-m-d') ? "today" : "";

        // Determine if the day is in the past or if it is booked
        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs' disabled>N/A</button>";
        } else {
            if (in_array($date, $bookings) || $dayOfWeek == 0 || $dayOfWeek == 6) {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs' disabled>N/A</button>";
            } else {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><a href='book.php?date=$date&room=$room' class='btn btn-success btn-xs'>Book</a>";
            }
        }

        $calendar .= "</td>";
        $currentDay++;
        $dayOfWeek++;
    }

    // Add blank cells for days of the week after the last day of the month
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td></td>";
        }
    }

    $calendar .= "</tr></table></div>";

    return $calendar; // Return the generated calendar HTML
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #007BFF;
            color: white;
        }
        .navbar a {
            color: white;
        }
        .calendar-container {
            margin-top: 20px;
        }
        .table {
            background-color: white;
        }
        .header {
            background-color: #4CAF50;
            color: white;
        }
        .today {
            background-color: yellow !important;
        }
        .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
        }
        .btn-danger {
            background-color: #DC3545;
            border-color: #DC3545;
        }
        .btn-success {
            background-color: #28A745;
            border-color: #28A745;
        }
        .btn-xs {
            font-size: 0.8em;
        }
        @media (max-width: 760px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            .empty {
                display: none;
            }
            th {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }
            td:nth-of-type(1):before { content: "Sunday"; }
            td:nth-of-type(2):before { content: "Monday"; }
            td:nth-of-type(3):before { content: "Tuesday"; }
            td:nth-of-type(4):before { content: "Wednesday"; }
            td:nth-of-type(5):before { content: "Thursday"; }
            td:nth-of-type(6):before { content: "Friday"; }
            td:nth-of-type(7):before { content: "Saturday"; }
        }
        @media (min-width: 641px) {
            table { table-layout: fixed; }
            td { width: 33%; }
        }
        .row {
            margin-top: 20px;
        }
    </style>
    <title>Booking Calendar</title>
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
    <div class="row">
        <div class="col-md-12">
            <?php
            // Get the current date components
            $dateComponents = getdate();
            
            // Check if month and year are set in the URL, otherwise use the current month and year
            if (isset($_GET['month']) && isset($_GET['year'])) {
                $month = $_GET['month'];
                $year = $_GET['year'];
            } else {
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];
            }

            // Check if room is set in the URL, otherwise use room 0
            if (isset($_GET['room'])) {
                $room = $_GET['room'];
            } else {
                $room = 0;
            }

            // Build and display the calendar
            echo build_calendar($month, $year, $room);
            ?>
        </div>
    </div>
</div>
</body>
</html>
