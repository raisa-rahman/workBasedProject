<?php

function desks($duration, $cleanup, $start, $end){
    $slots = array();
    $current = strtotime($start);
    $end = strtotime($end);
    
    // Loop to generate time slots
    while ($current + $duration * 60 <= $end) {
        $slotStart = date('H:i', $current);
        $current += ($duration + $cleanup) * 60;
        $slotEnd = date('H:i', $current);
        $slots[] = $slotStart . ' - ' . $slotEnd;
    }
    
    return $slots; // Return array of time slots
}
?>

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
        <a class="navbar-brand" href="index.php">Home</a> <!-- Link to home page -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a> <!-- Link to logout page -->
            </li>
        </ul>
    </div>
</header>

<div class="container">
    <h1 class="text-center">Welcome to the Desk Booking System</h1>
    <div class="text-center">
        <a href="create_booking.php" class="btn btn-primary">Create a Desk Booking</a>
        <a href="manage_bookings.php" class="btn btn-secondary">Manage My Bookings</a>
        <a href="in_office.php" class="btn btn-info">Who is in Office Now?</a>
    </div>
</div>

</body>
</html>

<<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Calendar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <style>
        .bordered-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .bordered-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .bordered-table th, .bordered-table td {
            text-align: center;
        }
        .desk-button {
            margin-bottom: 10px; /* Adjust the value to increase or decrease the gap */
        }
    </style>
</head>
<body>

<?php
   $year = isset($_GET['year']) ? $_GET['year'] : date("Y");
   $week = isset($_GET['week']) ? $_GET['week'] : date("W");

   // Adjusting year and week based on user input or current date
   if ($week > 53) {
       $year++;
       $week = 1;
   } elseif ($week < 1) {
       $year--;
       $week = 53;
   }

   // Variables for current date values
   $currentYear = date("Y");
   $currentWeek = date("W");
   $currentMonth = date("F");

   // Determining the start and end of the week
   $weekStart = new DateTime();
   $weekStart->setISODate($year, $week);
   $weekEnd = clone $weekStart;
   $weekEnd->modify('+6 days');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <center>
            <h1>Weekly Calendar</h1>
            <h2><?php echo $weekStart->format('F Y');?></h2>
            <!-- Navigation links for different weeks -->
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week + 1) . '&year=' . $year; ?>">Next Week</a>
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . $currentWeek . '&year=' . $currentYear; ?>">Current Week</a>
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week - 1) . '&year=' . $year; ?>">Previous Week</a>
            </center>
            <!-- Table for displaying the weekly calendar -->
            <table class="table bordered-table">
                <tr class="success">
                    <?php
                    // Displaying the days of the week
                    for ($day = 0; $day < 7; $day++) {
                        $d = clone $weekStart;
                        $d->modify("+$day days");
                        // Highlighting the current day
                        if ($d->format('Y-m-d') == date('Y-m-d')) {
                            echo "<td style='background:yellow'>" . $d->format('l') . "<br>" . $d->format('d M Y') . "</td>";
                        } else {
                            echo "<td>" . $d->format('l') . "<br>" . $d->format('d M Y') . "</td>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    // Displaying desks for each day of the week
                    for ($day = 0; $day < 7; $day++) {
                        echo "<td>";
                        for ($desk = 1; $desk <= 12; $desk++) {
                            echo "<button class='btn btn-default btn-sm desk-button'>Desk $desk</button><br>";
                        }
                        echo "</td>";
                    }
                    ?>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</body>
</html>