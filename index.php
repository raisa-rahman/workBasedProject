<?php
// Function to fetch available rooms from the database
function get_rooms() {
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
    $query = "SELECT * FROM rooms";  // Assuming you have a table named 'rooms'
    $result = $mysqli->query($query);

    $rooms = "";
    $first_room = 0;
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($i == 0) {
                $first_room = $row['id'];
            }
            $rooms .= "<option value='".$row['id']."'>".$row['name']."</option>";
            $i++;
        }
    }

    return $rooms;
}

//Function to build a calendar for a specific month and year
function build_calendar($month, $year, $room) {

    // Connect to the database
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

    // Prepare the query to retrieve the bookings for month and year
    $stmt = $mysqli->prepare('SELECT * FROM rooms');
    $rooms = "";
    // Execute query and fetch results
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms.= "<option value='".$row['id']."'>".$row['name']."</option>";
            }
        }
        $stmt->close();
    }

    if($room == 0){
        $first_room = $room;
    }

    // Prepare the query to retrieve the bookings for month and year
    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE MONTH(date) = ? AND YEAR(date) = ? AND ROOM_ID = ?");
    $stmt->bind_param('ssi', $month, $year, $first_room);

    // Array to store booking dates
    $bookings = array();

    // Execute query and fetch results
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row['date'];
            }
        }
        $stmt->close();
    }

    // Array of days of week
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

    // Get timestamp for first day of the month
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    // Get number of days in month
    $numberDays = date('t', $firstDayOfMonth);

    // Get date components for first day of month
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date('Y-m-d');

    // Calculate previous and next month/year
    $prev_month = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
    $prev_year = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
    $next_month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
    $next_year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

    // Get rooms to populate the dropdown
    $rooms = get_rooms();

    // Build calendar HTML
    $calendar = "<center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."'>Previous Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year."'>Next Month</a></center>";
    $calendar .= "
    <form id='room_select_form'>
        <div class='col-md-6 col-md-offset-3 form-group'>
            <label>Choose Room</label>
            <select class='form-control' id='room_select'name ='room'>
                ".$rooms."
            </select>
            <input type='hidden' name='month' value='".$month."'>
            <input type='hidden' name='year' value='".$year."'>
        </div>
    </form>
    <table class='table table-bordered'>";
    $calendar .= "<tr>";

    // Generate table headers for days of week
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";

    // Fill in empty cells before first day of month
    $currentDay = 1;
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td></td>";
        }
    }

    // Loop through each day of the month
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        // Format the date
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $today = $date == date('Y-m-d') ? "today" : "";

        // Check if date is in the past, available, or booked
        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button>";
        } else {
            // Check if the date is booked
            if (in_array($date, $bookings)) {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs' disabled>Booked</button>";
            } else {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><a href='book.php?date=$date' class='btn btn-success btn-xs'>Book</a>";
            }
        }

        $calendar .= "</td>";
        $currentDay++;
        $dayOfWeek++;
    }

    // Fill in empty cells after last day of month
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td></td>";
        }
    }

    $calendar .= "</tr></table>";

    return $calendar;
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        @media only screen and (max-width: 760px),
        (min-device-width: 802px) and (max-device-width: 1020px) {
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
            td:nth-of-type(1):before { content: "Monday"; }
            td:nth-of-type(2):before { content: "Tuesday"; }
            td:nth-of-type(3):before { content: "Wednesday"; }
            td:nth-of-type(4):before { content: "Thursday"; }
            td:nth-of-type(5):before { content: "Friday"; }
            td:nth-of-type(6):before { content: "Saturday"; }
            td:nth-of-type(7):before { content: "Sunday"; }
            @media only screen and (min-device-width: 320px) and (max-device-width: 480px) {
                body { padding: 0; margin: 0; }
            }
            @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
                body { width: 495px; }
            }
            @media (min-width: 641px) {
                table { table-layout: fixed; }
                td { width: 33%; }
            }
            .row { margin-top: 20px; }
            .today { background-color: yellow; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            $dateComponents = getdate();
            if (isset($_GET['month']) && isset($_GET['year'])) {
                $month = $_GET['month'];
                $year = $_GET['year'];
            } else {
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];
            }

            if (isset($_GET['date'])) {
                $date = $_GET['date'];
            }else{
                $room = 0;
            }

            echo build_calendar($month, $year, $room);
            ?>
        </div>
    </div>
</div>
<script src = "https://code.jquery.com/jquery-3.4.1.min.js" integrity = "sha256-CSXorXvZcTaix6Yvo6HppcZGetbYMGWSFIBw8HfCjO=" crossorigin = "anonymous"></script>
<script>
    $("#room_select").change(function() {
        $("#room_select_form").submit();
    });

    $("#room_select" option [value = "<?php echo $room; ?>"]).attr('selected', 'selected');
</script>



</body>
</html>