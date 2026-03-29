<?php
include("../config/db.php");

// Get auction code from URL
if(isset($_GET['code'])){
    $auction_code = mysqli_real_escape_string($conn, $_GET['code']);
    $auction_query = "SELECT auction_id, max_purse FROM Auction WHERE auction_code = '$auction_code'";
    $auction_result = mysqli_query($conn, $auction_query);
    if(mysqli_num_rows($auction_result) > 0){
        $auction = mysqli_fetch_assoc($auction_result);
        $auction_id = $auction['auction_id'];
        $max_purse = $auction['max_purse'];
    } else {
        $auction_id = null;
        $max_purse = 0;
    }
} else {
    $auction_code = null;
    $auction_id = null;
    $max_purse = 0;
}

if(isset($_POST['register'])){

    $team_name = $_POST['team_name'];
    $manager = $_POST['manager'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $team_logo = $_POST['team_logo'];
    $achievements = $_POST['acheivement'];

    $query = "INSERT INTO Team 
    (auction_id, team_name, team_logo, manager_name, achievements, purse_remaining, username, password, status)
    VALUES 
    ('$auction_id', '$team_name','$team_logo','$manager','$achievements','$max_purse','$username','$password','Pending')";

    if(mysqli_query($conn,$query)){
        echo "<script>alert('Registration Submitted'); window.location.href='login.php" . ($auction_code ? "?code=" . urlencode($auction_code) : "") . "';</script>";
    } else {
        echo "<script>alert('Registration Failed: " . mysqli_error($conn) . "');</script>";
    }
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

<?php if($auction_code): ?>
    <p style="color: #666; margin-bottom: 20px;">Auction Code: <strong><?php echo htmlspecialchars($auction_code); ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="team_name" placeholder="Team Name" required>
    <input type="url" name="team_logo" placeholder="Team Logo URL" required>
    <input type="text" name="acheivement" placeholder="Achievements">
    <input type="text" name="manager" placeholder="Manager Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<a href="login.php<?php echo $auction_code ? '?code=' . urlencode($auction_code) : ''; ?>">Already have account? Login</a>
</div>

</body>
</html>