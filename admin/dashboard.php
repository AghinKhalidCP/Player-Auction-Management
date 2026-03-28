<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = $_SESSION['auction_id'];

// Stats
$teams = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Team WHERE auction_id=$auction_id AND status='Approved'"));
$players = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Player WHERE auction_id=$auction_id AND status='Approved'"));
$sold = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Auction_Record WHERE auction_id=$auction_id AND result='SOLD'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="../index.php" style="text-decoration: none; color: white;"><h2>🏏 Player Auction System</h2></a>
    <div class="nav-right">
        <span>👤 Admin</span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>

        <a class="selected" href="dashboard.php">🏠 Dashboard</a>
        <a href="team_request.php">👥 Teams</a>
        <a href="player_request.php">🏏 Players</a>
        <a href="record_auction.php">💰 Auction Records</a>
        <a href="reports.php">📊 Reports</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h1>📊 Dashboard Overview</h1>

        <!-- STAT CARDS -->
        <div class="cards">

            <div class="card gradient-blue">
                <p>👥 Approved Teams</p>
                <h2><?php echo $teams; ?></h2>
            </div>

            <div class="card gradient-green">
                <p>🏏 Approved Players</p>
                <h2><?php echo $players; ?></h2>
            </div>

            <div class="card gradient-orange">
                <p>💰 Players Sold</p>
                <h2><?php echo $sold; ?></h2>
            </div>

        </div>

    </div>

</div>

</body>
</html>