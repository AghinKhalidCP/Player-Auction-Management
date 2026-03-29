<?php
include("../config/db.php");

// Get auction code from URL
if(isset($_GET['code'])){
    $auction_code = mysqli_real_escape_string($conn, $_GET['code']);
    $auction_query = "SELECT auction_id, game_type FROM Auction WHERE auction_code = '$auction_code'";
    $auction_result = mysqli_query($conn, $auction_query);
    if(mysqli_num_rows($auction_result) > 0){
        $auction = mysqli_fetch_assoc($auction_result);
        $auction_id = $auction['auction_id'];
        $game_type = strtolower($auction['game_type']);
    } else {
        $auction_id = null;
        $game_type = null;
    }
} else {
    $auction_code = null;
    $auction_id = null;
    $game_type = null;
}

// Determine positions based on game type
$positions = array();
if($game_type === 'cricket'){
    $positions = array('Batsman', 'Bowler', 'All-rounder', 'Wicket Keeper');
} elseif($game_type === 'football'){
    $positions = array('Goalkeeper', 'Defenders', 'Midfielders', 'Forwards');
}

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $age = $_POST['age'];
    $position = $_POST['position'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $playing_style = isset($_POST['playing_style']) ? $_POST['playing_style'] : '';
    $base_price = isset($_POST['base_price']) ? $_POST['base_price'] : 0;

    $query = "INSERT INTO Player 
    (auction_id, name, age, position, playing_style, phone, email, username, password, status)
    VALUES 
    ('$auction_id', '$name','$age','$position','$playing_style','$phone','$email','$username','$password','Pending')";

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
    <title>Player Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="login-box">
<h2>Player Registration</h2>

<?php if($auction_code): ?>
    <p style="color: #666; margin-bottom: 20px;">Auction Code: <strong><?php echo htmlspecialchars($auction_code); ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Player Name" required>
    <input type="number" name="age" placeholder="Age" required>
    <?php if(count($positions) > 0): ?>
    <select name="position" required>
        <option value="">Select Position</option>
        <?php foreach($positions as $pos): ?>
        <option value="<?php echo htmlspecialchars($pos); ?>"><?php echo htmlspecialchars($pos); ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <label for="playingstyle" style="display: block; text-align: left; margin-left: 26px; margin-bottom: -4px; font-size: 12px">Playing Style:</label>
    <input type="text" name="playing_style" placeholder="e.g., Right-arm fast bowler" required >
    <input type="email" name="email" placeholder="Email ID" required>
    <input type="tel" name="phone" placeholder="Phone Number" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<a href="login.php<?php echo $auction_code ? '?code=' . urlencode($auction_code) : ''; ?>">Already Registered? Login</a>
</div>

</body>
</html>