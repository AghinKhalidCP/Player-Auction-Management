<?php
include("config/db.php");

if(isset($_POST['submit'])){

    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];
    $auction_name = $_POST['auction_name'];
    $game_type = $_POST['game_type'];
    $venue = $_POST['venue'];
    $auction_date = $_POST['auction_date'];
    $auction_time = $_POST['auction_time'];
    $max_teams = $_POST['max_teams'];
    $max_players = $_POST['max_players'];
    $max_purse = $_POST['max_purse'];
    $min_base_price = $_POST['min_base_price'];
    $auction_code = "AUC" . rand(1000,9999);

    $admin_query = "INSERT INTO Admin (name,email,password)
                    VALUES ('$admin_name','$admin_email','$admin_password')";
    mysqli_query($conn,$admin_query);
    
    $auction_query = "INSERT INTO Auction 
    (auction_name, auction_code, game_type, venue, auction_date, auction_time, 
     max_teams, max_players_per_team, max_purse, min_base_price, status)
    VALUES 
    ('$auction_name', '$auction_code','$game_type','$venue','$auction_date','$auction_time',
     '$max_teams','$max_players','$max_purse','$min_base_price','Created')";

    mysqli_query($conn,$auction_query);

    echo "
<div class='popup'>
    <h3>Auction Created Successfully!</h3>
    <p>Your Auction Code:</p>
    
    <input type='text' id='auctionCode' value='$auction_code' readonly>
    <br><br>
    
    <button onclick='copyCode()'>Copy Code</button>
    <br><br>
    
    <a href='index.php'>Go to Home</a>
</div>

<script>
function copyCode() {
    var copyText = document.getElementById('auctionCode');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand('copy');
    alert('Auction code copied to clipboard!');
}
</script>
";

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Auction</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
<h2>Create Auction</h2>

<form method="POST">

<h3>Admin Details</h3>
<input type="text" name="admin_name" placeholder="Admin Name" required>
<input type="email" name="admin_email" placeholder="Admin Email" required>
<input type="password" name="admin_password" placeholder="Password" required>

<h3>Auction Details</h3>

<input type="text" name="auction_name" placeholder="Auction Name" required>

<select name="game_type" required>
<option value="">Select Game</option>
<option value="Cricket">Cricket</option>
<option value="Football">Football</option>
<option value="Other">Other</option>
</select>

<input type="text" name="venue" placeholder="Venue" required>
<input type="date" name="auction_date" required>
<input type="time" name="auction_time" required>

<input type="number" name="max_teams" placeholder="Max Teams" required>
<input type="number" name="max_players" placeholder="Max Players per Team" required>
<input type="number" name="max_purse" placeholder="Max Purse" required>
<input type="number" name="min_base_price" placeholder="Minimum Base Price" required>

<button type="submit" name="submit">Create Auction</button>

</form>
</div>

</body>
</html>
