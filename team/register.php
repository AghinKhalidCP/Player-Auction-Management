<?php
include("../config/db.php");

if(isset($_POST['register'])){

    $team_name = $_POST['team_name'];
    $manager = $_POST['manager'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "INSERT INTO Team 
    (team_name, manager_name, username, password, status)
    VALUES 
    ('$team_name','$manager','$username','$password','Pending')";

    mysqli_query($conn,$query);

    echo "<script>alert('Registration Submitted');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Team Registration</h2>

<form method="POST">
    <input type="text" name="team_name" placeholder="Team Name" required>
    <input type="text" name="manager" placeholder="Manager Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<a href="login.php">Already have account? Login</a>
</div>

</body>
</html>