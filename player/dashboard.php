<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['player_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = intval($_SESSION['auction_id'] ?? 0);
$player_id = intval($_SESSION['player_id']);

// Get player details
$player = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Player WHERE player_id=$player_id"));

if(!$player){
    header("Location: login.php");
    exit();
}

// Auction details
$auction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Auction WHERE auction_id=$auction_id"));

// Check if player is sold
$auction_record = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM Auction_Record 
    WHERE player_id=$player_id AND auction_id=$auction_id
"));

// Get team info if sold
$team_info = null;
if($auction_record && $auction_record['team_id'] > 0){
    $team_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Team WHERE team_id=" . intval($auction_record['team_id'])));
}

// Count of players in auction
$total_players = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Player WHERE auction_id=$auction_id AND LOWER(status)='approved'"));

// Count of approved teams
$total_teams = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Team WHERE auction_id=$auction_id AND LOWER(status)='approved'"));

// Count of sold players
$sold_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Auction_Record WHERE auction_id=$auction_id AND result='SOLD'"));

?>

<!DOCTYPE html>
<html>
<head>
    <title>Player Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="../index.php" style="text-decoration: none; color: white;"><h2>🏏 Player Auction System</h2></a>
    <div class="nav-right">
        <span>🎮 <?php echo isset($player['name']) ? $player['name'] : 'Player'; ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>

        <a class="selected" href="dashboard.php">🏠 Dashboard</a>
        <a href="my_team.php">🏏 My Teams</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="container-wide">

            <h1>📊 Player Dashboard</h1>

        <!-- Player Info and Stats Container -->
        <div style="display: flex; gap: 20px; margin-bottom: 30px;">

            <!-- Player Info Card -->
            <div class="card" style="background: white; color: #333; text-align: center; flex: 0 0 50%; min-width: 0;">
                <div style="padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 60px; border: 3px solid #007BFF;">🏏</div>
                    <h2 style="color: #2c3034; margin: 15px 0;"><?php echo isset($player['name']) ? $player['name'] : 'N/A'; ?></h2>
                    <p style="font-size: 18px; color: #666; margin: 10px 0;"><strong><?php echo isset($player['position']) ? $player['position'] : '-'; ?></strong></p>
                    <p style="font-size: 14px; color: #999; margin: 10px 0;">Age: <?php echo isset($player['age']) ? $player['age'] : '-'; ?></p>
                    <p style="font-size: 14px; color: #666; margin: 10px 0;">Style: <?php echo isset($player['playing_style']) ? $player['playing_style'] : '-'; ?></p>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div style="flex: 0 0 50%; display: flex; flex-direction: column; gap: 15px;">

                <div class="card gradient-blue">
                    <p>🏆 Auction Status</p>
                    <h2><?php echo ($auction_record && $auction_record['team_id'] > 0) ? 'SOLD ✅' : (($auction_record && $auction_record['team_id'] == 0) ? 'UNSOLD ❌' : 'PENDING'); ?></h2>
                </div>

                <div class="card gradient-orange">
                    <p>💰 Final Price</p>
                    <h2><?php echo ($auction_record && $auction_record['team_id'] > 0) ? '₹' . number_format($auction_record['final_price']) : '-'; ?></h2>
                </div>

                <div class="card gradient-green">
                    <p>👥 Total Players</p>
                    <h2><?php echo $total_players; ?></h2>
                </div>

            </div>

        </div>

        <!-- Sold Team Info -->
        <?php if($auction_record && $auction_record['team_id'] > 0 && $team_info): ?>
        <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #5ACD6D 100%); color: white; padding: 25px; margin-bottom: 30px;">
            <h3 style="margin-top: 0; margin-bottom: 15px;">🎯 Sold to Team</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p style="opacity: 0.9; margin: 5px 0; font-size: 14px;">Team Name</p>
                    <p style="font-weight: 700; font-size: 20px; margin: 0;"><?php echo $team_info['team_name']; ?></p>
                </div>
                <div>
                    <p style="opacity: 0.9; margin: 5px 0; font-size: 14px;">Price</p>
                    <p style="font-weight: 700; font-size: 20px; margin: 0;">₹<?php echo number_format($auction_record['final_price']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Auction Stats -->
        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div class="card gradient-blue" style="flex: 1;">
                <p>📊 Total Teams</p>
                <h2><?php echo $total_teams; ?></h2>
            </div>
            <div class="card gradient-orange" style="flex: 1;">
                <p>🎪 Players Sold</p>
                <h2><?php echo $sold_count; ?></h2>
            </div>
            <div class="card gradient-green" style="flex: 1;">
                <p>⏱️ Total Players</p>
                <h2><?php echo $total_players; ?></h2>
            </div>
        </div>

        </div>

    </div>

</div>

</body>
</html>
