<?php
function build_calender($month, $year){
    //create array of days of the week
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    
    //get first day of the month
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    //get number of days in the month
    $numberDays = date('t', $firstDayOfMonth);

    //get information about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);

    //get name of the month
    $monthName = $dateComponents['month'];

    //get index value of the first day of the month
    $dayOfWeek = ($dateComponents['wday'] + 6) % 7;

    //get current date
    $dateToday = date('Y-m-d');

    //create HTML table
    $calender = "<table class='table table-bordered'>";
    $calender .= "<center><h2>$monthName $year</h2></center>";
    
    $calender .= "<tr>";

    //create calender headers
    foreach($daysOfWeek as $day){
        $calender .= "<th class='header'>$day</th>";
    }

    $calender .= "</tr><tr>";

    //$dayOfWeek ensures there are only 7 columns on table
    if($daysOfWeek > 0){
        for($k=0; $k<$dayOfWeek; $k++){
            $calender .= "<td></td>";
        }
    }

    //initiate day counter
    $currentDay = 1;

    //get the month number
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while($currentDay <= $numberDays){

        //if is seventh coloumn (sunday), start a new row
        if($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calender .= "</tr><tr>";
        }


        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        if($dateToday == $date){
            $calender .= "<td class='today'>$currentDay</td>";
        }else{
            $calender .= "<td>$currentDay</td>";
        }

        //increment counters
        $currentDay++;
        $dayOfWeek++;

    }

    //complete the row of the last week in month, if necessary
    if($dayOfWeek != 7){
        $remainingDays = 7 - $dayOfWeek;
        for($i=0; $i<$remainingDays; $i++){
            $calender .= "<td></td>";
        }
    }

    $calender .= "</tr>";
    $calender .= "</table>";

    echo $calender;
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
                echo build_calender($month, $year);
                ?>
             </div>
            </div>
        </div>
    </body>
</html>
