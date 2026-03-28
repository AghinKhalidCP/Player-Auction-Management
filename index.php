<!DOCTYPE html>
<html>
<head>
    <title>Player Auction Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div style="padding: 40px 20px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Player Auction Record Management System</h1>

    <div style="display: flex; gap: 40px; flex-wrap: wrap; justify-content: center;">
        <div class="card">
            <h2 style="color: #2c3034;">Enter Auction Room</h2>
            <form action="join.php" method="POST">
                <input type="text" name="auction_code" placeholder="Enter Auction Code" required>
                <button type="submit">Join Auction</button>
            </form>
        </div>

        <div class="card">
            <h2 style="color: #2c3034;">Create Auction Room</h2>
            <a href="create_auction.php" class="btn">Create Auction</a>
        </div>
    </div>

</div>

</body>
</html>
