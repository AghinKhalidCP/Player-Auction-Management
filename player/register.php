<?php
include("../config/db.php");

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $age = $_POST['age'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "INSERT INTO Player 
    (name, age, username, password, status)
    VALUES 
    ('$name','$age','$username','$password','Pending')";

    mysqli_query($conn,$query);

    echo "<script>alert('Registration Submitted');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Player Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Player Registration</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Player Name" required>
    <input type="number" name="age" placeholder="Age" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<a href="login.php">Already Registered? Login</a>
</div>

</body>
</html>