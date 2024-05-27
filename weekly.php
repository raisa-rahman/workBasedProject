<?php

function desks($duration, $cleanup, $start, $end){
    
    $slots = array();
    return $slots;
}
?>
<!DOCTYPE html>
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

   if ($week > 53) {
       $year++;
       $week = 1;
   } elseif ($week < 1) {
       $year--;
       $week = 53;
   }

   $currentYear = date("Y");
   $currentWeek = date("W");
   $currentMonth = date("F");
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <center>
            <h1>Weekly Calendar</h1>
            <h2><?php echo $currentMonth . " " . $currentYear;?></h2>
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week + 1) . '&year=' . $year; ?>">Next Week</a>
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . $currentWeek . '&year=' . $currentYear; ?>">Current Week</a>
            <a class="btn btn-primary btn-xs" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week - 1) . '&year=' . $year; ?>">Previous Week</a>
            </center>
        <table class="table bordered-table">
                <tr class="success">
                    <?php
                    for ($day = 1; $day <= 7; $day++) {
                        $d = strtotime($year . "W" . str_pad($week, 2, "0", STR_PAD_LEFT) . $day);
                        if (date('d M Y', $d) == date('d M Y')) {
                            echo "<td style='background:yellow'>" . date('l', $d) . "<br>" . date('d M Y', $d) . "</td>";
                        } else {
                            echo "<td>" . date('l', $d) . "<br>" . date('d M Y', $d) . "</td>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    for ($day = 1; $day <= 7; $day++) {
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
