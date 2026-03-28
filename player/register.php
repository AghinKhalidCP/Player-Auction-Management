<?php
include("../config/db.php");

// Get auction code from URL
if(isset($_GET['code'])){
    $auction_code = mysqli_real_escape_string($conn, $_GET['code']);
    $auction_query = "SELECT auction_id FROM Auction WHERE auction_code = '$auction_code'";
    $auction_result = mysqli_query($conn, $auction_query);
    if(mysqli_num_rows($auction_result) > 0){
        $auction = mysqli_fetch_assoc($auction_result);
        $auction_id = $auction['auction_id'];
    } else {
        $auction_id = null;
    }
} else {
    $auction_code = null;
    $auction_id = null;
}

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $age = $_POST['age'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $base_price = isset($_POST['base_price']) ? $_POST['base_price'] : 0;

    $query = "INSERT INTO Player 
    (auction_id, name, age, username, password, phone, base_price, status)
    VALUES 
    ('$auction_id', '$name','$age','$username','$password','$phone','$base_price','Pending')";

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

<?php if($auction_code): ?>
    <p style="color: #666; margin-bottom: 20px;">Auction Code: <strong><?php echo htmlspecialchars($auction_code); ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Player Name" required>
    <input type="number" name="age" placeholder="Age" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="tel" name="phone" placeholder="Phone Number" required>
    <input type="number" name="base_price" placeholder="Base Price" required min="0">
    <button type="submit" name="register">Register</button>
</form>

<a href="login.php<?php echo $auction_code ? '?code=' . urlencode($auction_code) : ''; ?>">Already Registered? Login</a>
</div>

</body>
</html>