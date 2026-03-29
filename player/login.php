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

    // Step 1: Check player credentials and verify approved status
    $player_query = "SELECT * FROM Player 
                    WHERE username='$username' 
                    AND password='$password'";
    $player_result = mysqli_query($conn, $player_query);

    if(mysqli_num_rows($player_result) > 0){

        $player = mysqli_fetch_assoc($player_result);
        $player_id = isset($player['player_id']) ? $player['player_id'] : null;

        // Step 2: Check auction using URL code
        $auction_query = "SELECT * FROM Auction 
                          WHERE auction_code='$auction_code'";
        $auction_result = mysqli_query($conn, $auction_query);

        if(mysqli_num_rows($auction_result) > 0){

            $auction = mysqli_fetch_assoc($auction_result);

            // Step 3: Compare auction_id from player with auction_id from URL
            if($player_id !== null && (int)$player['auction_id'] === (int)$auction['auction_id']){

                // Step 4: Check if player is approved
                if(strtolower($player['status']) === 'approved'){

                    $_SESSION['player_id'] = $player_id;
                    $_SESSION['player'] = $username;
                    $_SESSION['player_name'] = $player['name'];
                    $_SESSION['auction_id'] = $auction['auction_id'];

                    echo "<script>
                            alert('Login Successful');
                            window.location.href='dashboard.php';
                          </script>";

                } else {
                    echo "<script>alert('Your profile is not approved yet');</script>";
                }

            } else {
                echo "<script>alert('Player not registered for this auction');</script>";
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
    <title>Player Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Player Login</h2>

<?php if($auction_code): ?>
    <p style="color: #666; margin-bottom: 20px;">Auction Code: <strong><?php echo htmlspecialchars($auction_code); ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" id="username" name="username" placeholder="Enter your username" required>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>
    
    <button type="submit" name="login">Login</button>
</form>

<a href="register.php<?php echo $auction_code ? '?code=' . urlencode($auction_code) : ''; ?>">New Player? Register</a>
</div>

</body>
</html>
