<?php

//Function to build a celender for a specific month and year
function build_calendar($month,$year){

    // connect to the database
    $mysqli = new mysqli('localhost','root','','bookingcalendar');

    //array of days of week
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
   
   // get timestamp for first day of the month
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    //get number of days in month
    $numberDays = date('t', $firstDayOfMonth);

    //get date components for first day of month
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    if($dayOfWeek == 0){
        $dayOfWeek = 6;
    }else{
        $dayOfWeek = $dayOfWeek - 1;
    }

    $dateToday = date('Y-m-d');

    //calculate previous and next month/year
    $prev_month = date('m', mktime(0,0,0,$month-1,1,$year));
    $prev_year = date('Y', mktime(0,0,0,$month-1,1,$year));
    $next_month = date('m', mktime(0,0,0,$month+1,1,$year));
    $next_year = date('Y', mktime(0,0,0,$month+1,1,$year));
    $calendar = "<center><h2>$monthName $year </h2>";

    //build calender HTML
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."'>Previous Month</a> ";
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year."'>Next Month</a></center>";
    $calendar.="<br><table class='table table-bordered'>";
    $calendar.="<tr>";

    //generate table headers for days of week
    foreach($daysOfWeek as $day){
        $calendar.="<th class='header'>$day</th>";
    }
    $calendar.="</tr><tr>";

    //fill in empty cells before first day of month
    $currentDay = 1;
    if($dayOfWeek > 0){
        for($k=0; $k<$dayOfWeek; $k++){
            $calendar.="<td></td>";
        }
    }

    //loop through each day of the month
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while($currentDay <= $numberDays){
        if($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calendar.="</tr><tr>";
        }

        //format the date
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        $dayName = strtolower(date('l', strtotime($date)));
        $today = $date == date('Y-m-d')? "today" : "";
    
        if ($dayName=='saturday' || $dayName=='sunday'){
            $calendar.="<td class='$today'><h4>$currentDay</h4><button class='btn bt-danger btn-xs'>N/A</button>";
        }elseif($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDay</h4><button class='btn bt-danger btn-xs'>N/A</button>";
        }else{

            $totalbookings = checkSlots($mysqli, $date);
            if($totalbookings == 11){
                $calendar.="<td class='$today'><h4>$currentDay</h4><a href = '#' class='btn bt-danger btn-xs'>All Booked</a>";
            }else{
                $availableSlots = 11 - $totalbookings;
            $calendar.="<td class='$today'><h4>$currentDay</h4><a href = 'book.php?date=".$date."' class='btn bt-success btn-xs'>Book</a><small><i>$availableSlots slots left </i></small>";
            }
        }

        
        $currentDay++;
        $dayOfWeek++;
    
    }

    //fill in empty cells afetr last day of month
    if($dayOfWeek != 7){
        $remainingDays = 7 - $dayOfWeek;
        for($i=0; $i<$remainingDays; $i++){
            $calendar.="<td></td>";
        }
    }

    $calendar.="</tr></table>";



    return $calendar;
}

function checkSlots($mysqli, $date){
    // prepare the query to retrieve the bookings for month and year
    $stmt = $mysqli->prepare("select * from bookings where date = ?");
    $stmt->bind_param('s', $date);

    // counter to store total bookings
    $totalbookings = 0;

    //execute query and fetch results
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if ($result -> num_rows > 0){
            while ($row = $result -> fetch_assoc()){
                $totalbookings++;
            }
            $stmt->close();
        }
    }
    return $totalbookings;
}



?>
<html>
    <head>
        <meta name ="viewport" content = "width = device-width, initial-scale = 1.0">
        <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <style>
            @media only screen and (max-width: 760px),
            (min-device-width 802px) and (max-device-width: 1020px){
                /*Force table to not be like tables anymore*/
                table,
                thead,
                tbody,
                th,
                td,
                tr{
                    display: block;
                }

                .empty{
                    display: none;
                }

                /*Hide table headers (but not display: none;, for accessibility)*/

                th{
                    position: absolute;
                    top: -9999px;
                    left: -9999px;
                }

                tr{
                    /*Behave like a "row" */
                    border: none;
                    border-bottom: 1px solid #eee;
                    position: relative;
                    padding-left: 50%;
                }

                /*Label the data */
                td:nth-of-type(1):before{
                    content: "Monday";
                }

                td:nth-of-type(2):before{
                    content: "Tuesday";
                }

                td:nth-of-type(3):before{
                    content: "Wednesday";
                }

                td:nth-of-type(4):before{
                    content: "Thursday";
                }

                td:nth-of-type(5):before{
                    content: "Friday";
                }

                td:nth-of-type(6):before{
                    content: "Saturday";
                }

                td:nth-of-type(7):before{
                    content: "Sunday";
                }

                /*Smartphone view*/
                @media only screen and (min-device-width: 320px) and (max-device-width: 480px){
                    body{
                        padding: 0;
                        margin: 0;
                    }
                }

                /*tablet view*/
                @media only screen and (min-device-width: 768px) and (max-device-width: 1024px){
                    body{
                        width: 495px;
                    }
                }

                @media (min-width: 641px){
                    table{
                        table-layout:fixed;
                    }

                    td{
                    width: 33%;
                    }
                }

                .row{
                    margin-top: 20px;
                }

                .today{
                    background-color: yellow;
                }
            
            </style>
        </head>

<body>
    <div class = "container">
        <div class = "row">
            <div class = "col-md-12">
            <?php
                $dateComponents = getdate();
        if(isset($_GET['month'])&& isset($_GET['year'])){
            $month = $_GET['month'];
            $year = $_GET['year'];
        }else{
            $month = $dateComponents['mon'];
            $year = $dateComponents['year'];
        }

        echo build_calendar($month, $year);
        ?>

        </div>
        </div>
        </div>
        </body>
        </html>