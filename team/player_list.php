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

// Get all approved players for the auction
$players_query = mysqli_query($conn, "
    SELECT player_id, name, age, position, achievements, phone, email
    FROM Player
    WHERE auction_id=$auction_id AND LOWER(status)='approved'
    ORDER BY name");

$player_count = mysqli_num_rows($players_query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Players List</title>
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
        <a href="my_team.php">👥 My Team</a>
        <a class="selected" href="player_list.php">🏏 Players List</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h1>🏏 Available Players</h1>

        <!-- Players Table -->
        <div class="card" style="background: white; color: #333;">
            <h3 style="color: #007BFF; margin-bottom: 15px;">Available Players for Auction</h3>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Position</th>
                        <th>Achievements</th>
                        <th>Phone</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $displayed_count = 0;
                    while($player = mysqli_fetch_assoc($players_query)){ 
                        $displayed_count++;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($player['name']); ?></td>
                        <td><?php echo htmlspecialchars($player['age']); ?></td>
                        <td><?php echo htmlspecialchars($player['position']); ?></td>
                        <td><?php echo htmlspecialchars($player['achievements']); ?></td>
                        <td><?php echo htmlspecialchars($player['phone']); ?></td>
                        <td><?php echo htmlspecialchars($player['email']); ?></td>
                    </tr>
                    <?php } 
                    if($displayed_count == 0) {
                        echo '<tr><td colspan="6" style="text-align: center; color: #999; padding: 40px;">No approved players available</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <p style="margin-top: 20px; color: #666; text-align: right;">
                <strong>Total Players: <?php echo $player_count; ?></strong>
            </p>
        </div>

    </div>
</div>

</body>
</html>
