<?php
session_start();

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // Successful login
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: main.php");
    } else {
        // Invalid login
        $error = "Invalid username or password.";
    }

    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .login-box {
            border: 2px solid #4a148c;
            border-radius: 10px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        .login-box h2 {
            color: #4a148c;
            margin-bottom: 20px;
        }
        .form-group label {
            color: #4a148c;
        }
        .btn-primary {
            background-color: #4a148c;
            border-color: #4a148c;
        }
        .alert-danger {
            color: #d32f2f;
            background-color: #ffebee;
            border-color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-box">
                    <h2>Login to Integrella's Desk Reservation System</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php

// Example script to create a new user with hashed password
$username = 'test';
$password = 'test123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
$stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param('ss', $username, $hashed_password);
$stmt->execute();
$stmt->close();
$mysqli->close();
?>
