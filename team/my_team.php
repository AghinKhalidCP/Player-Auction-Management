<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['team_id'])){
    header("Location: login.php");
    exit();
}

$team_id = intval($_SESSION['team_id']);

// Get team details
$team = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Team WHERE team_id=$team_id"));

if(!$team){
    header("Location: login.php");
    exit();
}

// Get team players
$my_players = mysqli_query($conn, "
    SELECT Player.player_id, Player.name, Player.position, Auction_Record.final_price
    FROM Auction_Record
    JOIN Player ON Player.player_id = Auction_Record.player_id
    WHERE Auction_Record.team_id = $team_id
    ORDER BY Player.name");

$player_count = mysqli_num_rows($my_players);
$total_spent = 0;
while($p = mysqli_fetch_assoc($my_players)){
    $total_spent += $p['final_price'];
}
mysqli_data_seek($my_players, 0);

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Team</title>
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

        <a href="dashboard.php">🏠 Dashboard</a>
        <a class="selected" href="my_team.php">👥 My Team</a>
        <a href="player_list.php">🏏 Players List</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h1>👥 My Team</h1>

        <!-- STAT CARDS -->
        <div class="cards">

            <div class="card gradient-blue">
                <p>👥 Team Name</p>
                <h2><?php echo isset($team['team_name']) ? $team['team_name'] : 'N/A'; ?></h2>
            </div>

            <div class="card gradient-green">
                <p>💰 Purse Remaining</p>
                <h2>₹<?php echo isset($team['purse_remaining']) ? number_format($team['purse_remaining']) : '0'; ?></h2>
            </div>

            <div class="card gradient-orange">
                <p>🏏 Total Players</p>
                <h2><?php echo $player_count; ?></h2>
            </div>

        </div>

        <!-- My Team Players -->
        <div class="card" style="background: white; color: #333; margin-top: 30px;">
            <h3 style="color: #007BFF; margin-bottom: 15px;">🏏 Team Players</h3>

            <table>
                <thead>
                    <tr>
                        <th>Player Name</th>
                        <th>Position</th>
                        <th>Base Price</th>
                        <th>Final Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $player_count_display = 0;
                    while($p = mysqli_fetch_assoc($my_players)){ 
                        $player_count_display++;
                    ?>
                    <tr>
                        <td><?php echo $p['name']; ?></td>
                        <td><?php echo $p['position']; ?></td>
                        <td>₹<?php echo number_format($p['base_price']); ?></td>
                        <td>₹<?php echo number_format($p['final_price']); ?></td>
                    </tr>
                    <?php } 
                    if($player_count_display == 0) {
                        echo '<tr><td colspan="4" style="text-align: center; color: #999; padding: 40px;">No players in your team yet</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
