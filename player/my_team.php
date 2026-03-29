<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['player_id'])) {
    header("Location: login.php");
    exit();
}

$player_id = intval($_SESSION['player_id']);
$auction_id = intval($_SESSION['auction_id'] ?? 0);

$player = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM Player
    WHERE player_id = $player_id
"));

if (!$player) {
    header("Location: login.php");
    exit();
}

$auction = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM Auction
    WHERE auction_id = $auction_id
"));

$auction_record = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM Auction_Record
    WHERE player_id = $player_id AND auction_id = $auction_id
    ORDER BY auction_id DESC
    LIMIT 1
"));

$team = null;
$team_mates = [];
if ($auction_record && intval($auction_record['team_id']) > 0) {
    $team_id = intval($auction_record['team_id']);
    $team = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT *
        FROM Team
        WHERE team_id = $team_id
    "));
    
    $team_mates_result = mysqli_query($conn, "
        SELECT p.*
        FROM Player p
        JOIN Auction_Record ar ON p.player_id = ar.player_id
        WHERE ar.team_id = $team_id AND ar.auction_id = $auction_id AND ar.result = 'SOLD'
        ORDER BY p.name ASC
    ");
    
    while ($mate = mysqli_fetch_assoc($team_mates_result)) {
        $team_mates[] = $mate;
    }
}

$auction_status = "PENDING";
if ($auction_record) {
    if (strtoupper($auction_record['result']) === "SOLD" && intval($auction_record['team_id']) > 0) {
        $auction_status = "SOLD";
    } elseif (strtoupper($auction_record['result']) === "UNSOLD" || intval($auction_record['team_id']) === 0) {
        $auction_status = "UNSOLD";
    }
}

$status_text = "Waiting for auction result";
if ($auction_status === "SOLD") {
    $status_text = "Sold to " . ($team['team_name'] ?? "a team");
} elseif ($auction_status === "UNSOLD") {
    $status_text = "Not sold in the auction";
}

$sold_count = mysqli_num_rows(mysqli_query($conn, "
    SELECT *
    FROM Auction_Record
    WHERE auction_id = $auction_id AND result = 'SOLD'
"));

$approved_team_count = mysqli_num_rows(mysqli_query($conn, "
    SELECT *
    FROM Team
    WHERE auction_id = $auction_id AND LOWER(status) = 'approved'
"));

$approved_player_count = mysqli_num_rows(mysqli_query($conn, "
    SELECT *
    FROM Player
    WHERE auction_id = $auction_id AND LOWER(status) = 'approved'
"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Team</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="navbar">
    <a href="../index.php"><h2>🏏 Player Auction System</h2></a>
    <div class="nav-right">
        <span>🎮 <?php echo htmlspecialchars($player['name'] ?? 'Player'); ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a class="selected" href="my_team.php">🏏 My Team</a>
    </div>

    <div class="main">
        <div class="container-wide">
            <h1>🏏 My Team</h1>

            <div class="table-card">
                <h3>🏏 Player Details</h3>
                <table>
                    <tbody>
                        <tr>
                            <th>🏏 Name</th>
                            <td><?php echo htmlspecialchars($player['name'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>📍 Position</th>
                            <td><?php echo htmlspecialchars($player['position'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>🎂 Age</th>
                            <td><?php echo htmlspecialchars($player['age'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>🎯 Playing Style</th>
                            <td><?php echo htmlspecialchars($player['playing_style'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>✅ Status</th>
                            <td><?php echo htmlspecialchars($player['status'] ?? '-'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <h3>🎯 Selected Team</h3>
                <?php if ($team && intval($auction_record['team_id']) > 0): ?>
                    <table>
                        <tbody>
                            <tr>
                                <th>🏷️ Team Name</th>
                                <td><?php echo htmlspecialchars($team['team_name'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>👔 Manager</th>
                                <td><?php echo htmlspecialchars($team['manager_name'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>🏅 Achievements</th>
                                <td><?php echo htmlspecialchars($team['achievements'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>💼 Purse Remaining</th>
                                <td>&#8377;<?php echo number_format((int) ($team['purse_remaining'] ?? 0)); ?></td>
                            </tr>
                            <tr>
                                <th>💸 Auction Price</th>
                                <td>&#8377;<?php echo number_format((int) $auction_record['final_price']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>❌ No team selected</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($team_mates)): ?>
            <div class="table-card">
                <h3>👥 Team Mates</h3>
                <table>
                    <thead>
                        <tr>
                            <th>🏏 Player Name</th>
                            <th>📍 Position</th>
                            <th>🎂 Age</th>
                            <th>🎯 Playing Style</th>
                            <th>✅ Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($team_mates as $mate): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mate['name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mate['position'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mate['age'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mate['playing_style'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mate['status'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
