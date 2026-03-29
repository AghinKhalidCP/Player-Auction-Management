<?php
include("../config/db.php");
session_start();

// Get auction code from URL
if(isset($_GET['code'])){
    $auction_code = mysqli_real_escape_string($conn, $_GET['code']);
} else {
    // If no code, redirect back
    header("Location: ../index.php");
    exit();
}

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Step 1: Check team credentials
    $team_query = "SELECT * FROM Team 
                    WHERE username='$username' 
                    AND password='$password'";
    $team_result = mysqli_query($conn, $team_query);

    if(mysqli_num_rows($team_result) > 0){

        $team = mysqli_fetch_assoc($team_result);
        $team_id = isset($team['team_id']) ? $team['team_id'] : null;

        // Step 2: Check auction using URL code
        $auction_query = "SELECT * FROM Auction 
                          WHERE auction_code='$auction_code'";
        $auction_result = mysqli_query($conn, $auction_query);

        if(mysqli_num_rows($auction_result) > 0){

            $auction = mysqli_fetch_assoc($auction_result);

            // Step 3: Compare auction_id from team with auction_id from URL
            if($team_id !== null && (int)$team['auction_id'] === (int)$auction['auction_id']){

                $_SESSION['team_id'] = $team_id;
                $_SESSION['team'] = $username;
                $_SESSION['team_name'] = $team['team_name'];
                $_SESSION['auction_id'] = $auction['auction_id'];

                echo "<script>
                        alert('Login Successful');
                        window.location.href='dashboard.php';
                      </script>";

            } else {
                echo "<script>alert('Team not registered for this auction');</script>";
            }

        } else {
            echo "<script>alert('Invalid Auction Code');</script>";
        }

    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Team Login</h2>

<?php if($auction_code): ?>
    <p style="color: #666; margin-bottom: 20px;">Auction Code: <strong><?php echo htmlspecialchars($auction_code); ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<a href="register.php<?php echo $auction_code ? '?code=' . urlencode($auction_code) : ''; ?>">New Team? Register</a>
</div>

</body>
</html>