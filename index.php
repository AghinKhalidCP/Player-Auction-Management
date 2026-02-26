<!DOCTYPE html>
<html>
<head>
    <title>Player Auction Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h1>Player Auction Record Management System</h1>

    <div class="card">
        <h2>Enter Auction Room</h2>
        <form action="join.php" method="POST">
            <input type="text" name="auction_code" placeholder="Enter Auction Code" required>
            <button type="submit">Join Auction</button>
        </form>
    </div>

    <div class="card">
        <h2>Create Auction Room</h2>
        <a href="create_auction.php" class="btn">Create Auction</a>
    </div>

</div>

</body>
</html>
