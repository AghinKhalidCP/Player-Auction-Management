<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['team_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = intval($_SESSION['auction_id'] ?? 0);
$team_id = intval($_SESSION['team_id']);

// Get team details
$team = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Team WHERE team_id=$team_id"));

if(!$team){
    header("Location: login.php");
    exit();
}

// Auction details
$auction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Auction WHERE auction_id=$auction_id"));

// My team players
$my_players = mysqli_query($conn, "
    SELECT Player.name, Player.position, Auction_Record.final_price
    FROM Auction_Record
    JOIN Player ON Player.player_id = Auction_Record.player_id
    WHERE Auction_Record.team_id = $team_id");

// Other teams
$other_teams_result = mysqli_query($conn, "SELECT team_name FROM Team WHERE auction_id=$auction_id AND team_id != $team_id AND LOWER(status)='approved' ORDER BY team_name");

// Auction statistics
$sold_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Auction_Record WHERE auction_id=$auction_id AND result='SOLD'"));
$total_teams = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Team WHERE auction_id=$auction_id AND LOWER(status)='approved'"));

// Auction progress (sold players)
$progress = mysqli_query($conn, "
    SELECT Player.name, Team.team_name, Auction_Record.final_price
    FROM Auction_Record
    JOIN Player ON Player.player_id = Auction_Record.player_id
    JOIN Team ON Team.team_id = Auction_Record.team_id
    WHERE Auction_Record.auction_id = $auction_id
    AND Auction_Record.result='SOLD'
    AND LOWER(Player.status)='approved'
    AND LOWER(Team.status)='approved'");

?>


<!DOCTYPE html>
<html>
<head>
    <title>Team Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="../index.php" style="text-decoration: none; color: white;"><h2>🏏 Player Auction System</h2></a>
    <div class="nav-right">
        <span>👥 <?php echo isset($team['team_name']) ? $team['team_name'] : 'Team'; ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>

        <a class="selected" href="dashboard.php">🏠 Dashboard</a>
        <a href="my_team.php">👥 My Team</a>
        <a href="player_list.php">🏏 Players List</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h1>📊 Team Dashboard</h1>

        <!-- Team Info and Stats Container -->
        <div style="display: flex; gap: 20px; margin-bottom: 30px;">

            <!-- Team Info Card -->
            <div class="card" style="background: white; color: #333; text-align: center; flex: 0 0 50%; min-width: 0;">
                <div style="padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 60px; border: 3px solid #007BFF;">🏏</div>
                    <h2 style="color: #2c3034; margin: 15px 0;"><?php echo isset($team['team_name']) ? $team['team_name'] : 'N/A'; ?></h2>
                    <p style="font-size: 28px; color: #27ae60; font-weight: bold; margin: 15px 0;">₹<?php echo isset($team['purse_remaining']) ? number_format($team['purse_remaining']) : '0'; ?></p>
                    <p style="color: #666; margin: 0;">Purse Remaining</p>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div style="flex: 0 0 50%; display: flex; flex-direction: column; gap: 15px;">

                <div class="card gradient-blue">
                    <p>🏏 Players Owned</p>
                    <h2><?php echo mysqli_num_rows($my_players); ?></h2>
                </div>

                <div class="card gradient-orange">
                    <p>💼 Players Sold (Auction)</p>
                    <h2><?php echo $sold_count; ?></h2>
                </div>

                <div class="card gradient-green">
                    <p>👥 Total Teams</p>
                    <h2><?php echo $total_teams; ?></h2>
                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>