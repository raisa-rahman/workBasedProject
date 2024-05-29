<?php
// Function to fetch available rooms from the database
function get_rooms() {
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
    $query = "SELECT * FROM rooms";
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
    $mysqli->close();  // Close the connection
    return $rooms;
}

//Function to build a calendar for a specific month and year
function build_calendar($month, $year, $room) {
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

    // Default room to first available if not specified
    if ($room == 0) {
        $room = $mysqli->query("SELECT id FROM rooms LIMIT 1")->fetch_assoc()['id'];
    }

    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE MONTH(date) = ? AND YEAR(date) = ? AND ROOM_ID = ?");
    $stmt->bind_param('iii', $month, $year, $room);  // Changed 'ssi' to 'iii' since month and year should be integers

    $bookings = array();
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row['date'];
            }
        }
        $stmt->close();
    }
    $mysqli->close();  // Close the connection

    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); // Changed order of days

    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date('Y-m-d');

    $prev_month = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
    $prev_year = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
    $next_month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
    $next_year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

    $rooms = get_rooms();

    $calendar = "<center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."&room=".$room."'>Previous Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."&room=".$room."'>Current Month</a> ";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year."&room=".$room."'>Next Month</a></center>";
    $calendar .= "
    <form id='room_select_form'>
        <div class='col-md-6 col-md-offset-3 form-group'>
            <label>Choose Room</label>
            <select class='form-control' id='room_select' name='room'>
                ".$rooms."
            </select>
            <input type='hidden' name='month' value='".$month."'>
            <input type='hidden' name='year' value='".$year."'>
        </div>
    </form>
    <table class='table table-bordered'>";
    $calendar .= "<tr>";

    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td></td>";
        }
    }

    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $today = $date == date('Y-m-d') ? "today" : "";

        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button>";
        } else {
            if (in_array($date, $bookings)) {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs' disabled>Booked</button>";
            } else {
                $calendar .= "<td class='$today'><h4>$currentDay</h4><a href='book.php?date=$date&room=$room' class='btn btn-success btn-xs'>Book</a>";
            }
        }

        $calendar .= "</td>";
        $currentDay++;
        $dayOfWeek++;
    }

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
            td:nth-of-type(1):before { content: "Sunday"; }
            td:nth-of-type(2):before { content: "Monday"; }
            td:nth-of-type(3):before { content: "Tuesday"; }
            td:nth-of-type(4):before { content: "Wednesday"; }
            td:nth-of-type(5):before { content: "Thursday"; }
            td:nth-of-type(6):before { content: "Friday"; }
            td:nth-of-type(7):before { content: "Saturday"; }
        }
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

            if (isset($_GET['room'])) {
                $room = $_GET['room'];
            } else {
                $room = 0;
            }

            echo build_calendar($month, $year, $room);
            ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTaix6Yvo6HppcZGetbYMGWSFIBw8HfCjO=" crossorigin="anonymous"></script>
<script>
    $("#room_select").change(function() {
        $("#room_select_form").submit();
    });

    $("#room_select option[value='<?php echo $room; ?>']").attr('selected', 'selected');
</script>
</body>
</html>
