<?php
if(isset($_GET['date'])){
  $date = $_GET['date'];
}

if(isset($_POST['submit'])){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
  $stmt = $mysqli->prepare("INSERT INTO bookings (name, email, date, timeslot) values (?,?,?,?)");
  $stmt->bind_param('ssss', $name, $email, $date, $_POST['timeslot']);
  $stmt->execute();
  $msg = "<div class='alert alert-success'>Booking Successfull</div>";
  $stmt->close();
  $mysqli->close();
}

$timeslot_options = array(
  "9:00AM - 1:00PM",
  "1:00PM - 5:00PM",
  "9:00AM - 5:00PM"
);

$duration = 200;
$cleanup = 0;
$start = "09:00"; 
$end = "17:00";

function timeslots($duration, $cleanup, $start, $end){
  $start = new DateTime($start);
  $end = new DateTime($end);
  $interval = new DateInterval("PT".$duration."M");
  $cleanupInterval = new DateInterval('PT'.$cleanup."M");

  $slots = array(); // Initialize $slots array

  for($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)){
    $endPeriod = clone $intStart;
    $endPeriod->add($interval);
    if($endPeriod > $end){
      break;
    }
    $slots[] = $intStart->format('H:iA')."-".$endPeriod->format('H:iA');
  }

  return $slots; // Return $slots array
}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
<div class="container">
    <h1 class="text-center">Book for Date: <?php echo date('F d,Y', strtotime($date)); ?> </h1><hr>
    <div class="row">
        <?php foreach($timeslot_options as $option): ?>
            <div class="col-md-4">
                  <?php
                    $disabled = ""; // Determine if the option should be disabled
                    // Check if the timeslot is already booked
                    // If it's already booked, disable the option
                    if(checkIfTimeslotBooked($option)) {
                        $disabled = "disabled";
                    }
                ?>
                <form method="post">
                    <input type="hidden" name="timeslot" value="<?php echo $option; ?>">
                    <button class="btn btn-success <?php echo $disabled; ?>"><?php echo $option; ?></button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Function to check if a timeslot is already booked
function checkIfTimeslotBooked($timeslot) {
    // Implement your logic here to check if the timeslot is booked
    // You can query your database to check if any bookings overlap with the provided timeslot
    // Return true if booked, false otherwise
    return false; // Sample implementation assuming no bookings exist
}
?>