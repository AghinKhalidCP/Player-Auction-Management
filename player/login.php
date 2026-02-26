<?php
include("../config/db.php");
session_start();

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM Player 
              WHERE username='$username' 
              AND password='$password' 
              AND status='Approved'";

    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) > 0){
        $_SESSION['player'] = $username;
        echo "<script>alert('Login Successful');</script>";
    } else {
        echo "<script>alert('Invalid or Not Approved');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Player Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Player Login</h2>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<a href="register.php">New Player? Register</a>
</div>

</body>
</html>