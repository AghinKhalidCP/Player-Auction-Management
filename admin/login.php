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

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Step 1: Check admin credentials
    $admin_query = "SELECT * FROM Admin 
                    WHERE email='$email' AND password='$password'";
    $admin_result = mysqli_query($conn, $admin_query);

    if(mysqli_num_rows($admin_result) > 0){

        $admin = mysqli_fetch_assoc($admin_result);
        $admin_id = isset($admin['admin_id']) ? $admin['admin_id'] : null;

        // Step 2: Check auction using URL code
        $auction_query = "SELECT * FROM Auction 
                          WHERE auction_code='$auction_code'";
        $auction_result = mysqli_query($conn, $auction_query);

        if(mysqli_num_rows($auction_result) > 0){

            $auction = mysqli_fetch_assoc($auction_result);

            // Step 3: Compare admin_id (cast to int to avoid type/coercion issues)
            if($admin_id !== null && (int)$auction['admin_id'] === (int)$admin_id){

                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['auction_id'] = $auction['auction_id'];

                echo "<script>
                        alert('Login Successful');
                        window.location.href='dashboard.php';
                      </script>";

            } else {
                echo "<script>alert('Not authorized for this auction');</script>";
            }

        } else {
            echo "<script>alert('Invalid Auction Code');</script>";
        }

    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<p>Auction Code: <strong><?php echo $auction_code; ?></strong></p>
<div class="login-box">
<h2>Admin Login</h2>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<a href="../index.php">Back</a>
</div>

</body>
</html>