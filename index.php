<?php
function build_calendar($month,$year){

    /*$mysqli = new mysqli('localhost','root','','bookingcalendar');
    $stmt = $mysqli->prepare("select * from bookings where MONTH(date) = ? AND YEAR(date) = ?");
    $stmt->bind_param('ss', $month, $year);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if ($result -> num_rows . 0){
            while ($row = $result -> fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }*/

    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date('Y-m-d');

    $prev_month = date('m', mktime(0,0,0,$month-1,1,$year));
    $prev_year = date('Y', mktime(0,0,0,$month-1,1,$year));
    $next_month = date('m', mktime(0,0,0,$month+1,1,$year));
    $next_year = date('Y', mktime(0,0,0,$month+1,1,$year));
    $calendar = "<center><h2>$monthName $year </h2>";
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."'>Previous Month</a> ";
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
    $calendar.="<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year."'>Next Month</a></center>";
    $calendar.="<br><table class='table table-bordered'>";
    $calendar.="<tr>";
    foreach($daysOfWeek as $day){
        $calendar.="<th class='header'>$day</th>";
    }

    $calendar.="</tr><tr>";
    $currentDay = 1;
    if($dayOfWeek > 0){
        for($k=0; $k<$dayOfWeek; $k++){
            $calendar.="<td></td>";
        }
    }

    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while($currentDay <= $numberDays){
        if($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calendar.="</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayName = strtolower(date('l', strtotime($date)));
        $today = $date == date('Y-m-d')? "today" : "";
       /* if(in_array($date, $bookings)){
            $calendar.="<td class='$today'><h4>$currentDay</h4><a class='btn bt-danger btn-xs'>Booked</a></td>";
        }else{
            $calendar.="<td class='$today'><h4>$currentDay</h4><a class='btn bt-success btn-xs book-button'>Book</a></td>";
        }   */
        
        if($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDay</h4><button class='btn bt-danger btn-xs'>N/A</button>";

        }else{
            $calendar.="<td class='$today'><h4>$currentDay</h4><a href = 'book.php?date=".$date."' class='btn bt-success btn-xs'>Book</a>";
        }

        
        $currentDay++;
        $dayOfWeek++;
    
    }

    if($dayOfWeek != 7){
        $remainingDays = 7 - $dayOfWeek;
        for($i=0; $i<$remainingDays; $i++){
            $calendar.="<td></td>";
        }
    }

    $calendar.="</tr></table>";



    return $calendar;
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
