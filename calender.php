<?php

// Function to build a calendar for a specific month and year
function build_calendar($month, $year){
    // Create array of days of the week
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    
    // Get first day of the month
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    // Get number of days in the month
    $numberDays = date('t', $firstDayOfMonth);

    // Get information about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);

    // Get name of the month
    $monthName = $dateComponents['month'];

    // Get index value of the first day of the month
    $dayOfWeek = ($dateComponents['wday'] + 6) % 7;

    // Get current date
    $dateToday = date('Y-m-d');

    // Create HTML table
    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'>Previous Month</a>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month</a></center>";

    $calendar .= "<tr>";

    // Create calendar headers
    foreach($daysOfWeek as $day){
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";

    // $dayOfWeek ensures there are only 7 columns on table
    if($dayOfWeek > 0){
        for($k = 0; $k < $dayOfWeek; $k++){
            $calendar .= "<td></td>";
        }
    }

    // Initiate day counter
    $currentDay = 1;

    // Get the month number
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while($currentDay <= $numberDays){

        // If it's the seventh column (Sunday), start a new row
        if($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        if($dateToday == $date){
            $calendar .= "<td class='today'>$currentDay</td>";
        } else {
            $calendar .= "<td>$currentDay</td>";
        }

        // Increment counters
        $currentDay++;
        $dayOfWeek++;

    }

    // Complete the row of the last week in month, if necessary
    if($dayOfWeek != 7){
        $remainingDays = 7 - $dayOfWeek;
        for($i = 0; $i < $remainingDays; $i++){
            $calendar .= "<td></td>";
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";

    return $calendar;
}

?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $dateComponents = getdate();
                    $month = $dateComponents['mon'];
                    $year = $dateComponents['year'];
                    echo build_calendar($month, $year);
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
