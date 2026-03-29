<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = $_SESSION['auction_id'];

// Get auction details
$auction = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM Auction
    WHERE auction_id = $auction_id
"));

// Get all auction records with player and team info
$auction_records = mysqli_query($conn, "
    SELECT ar.*, p.name as player_name, p.position, t.team_name
    FROM Auction_Record ar
    JOIN Player p ON ar.player_id = p.player_id
    LEFT JOIN Team t ON ar.team_id = t.team_id
    WHERE ar.auction_id = $auction_id
    ORDER BY ar.record_id DESC
");

// Get all teams with player count
$teams = mysqli_query($conn, "
    SELECT t.*, 
           COUNT(CASE WHEN ar.result = 'SOLD' THEN 1 END) as players_sold,
           SUM(CASE WHEN ar.result = 'SOLD' THEN ar.final_price ELSE 0 END) as total_spent
    FROM Team t
    LEFT JOIN Auction_Record ar ON t.team_id = ar.team_id AND ar.auction_id = $auction_id
    WHERE t.auction_id = $auction_id
    GROUP BY t.team_id
    ORDER BY t.team_name ASC
");

// Get all approved players
$players = mysqli_query($conn, "
    SELECT p.*, 
           COALESCE(ar.result, 'PENDING') as auction_result,
           COALESCE(ar.final_price, 0) as sold_price,
           t.team_name
    FROM Player p
    LEFT JOIN Auction_Record ar ON p.player_id = ar.player_id AND ar.auction_id = $auction_id
    LEFT JOIN Team t ON ar.team_id = t.team_id
    WHERE p.auction_id = $auction_id AND LOWER(p.status) = 'approved'
    ORDER BY p.name ASC
");

// Get revenue summary
$revenue_summary = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(DISTINCT CASE WHEN result = 'SOLD' THEN player_id END) as total_sold,
        COUNT(DISTINCT CASE WHEN result = 'UNSOLD' THEN player_id END) as total_unsold,
        SUM(CASE WHEN result = 'SOLD' THEN final_price ELSE 0 END) as total_revenue,
        AVG(CASE WHEN result = 'SOLD' THEN final_price ELSE NULL END) as avg_price
    FROM Auction_Record
    WHERE auction_id = $auction_id
"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
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

        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="team_request.php">👥 Teams</a>
        <a href="player_request.php">🏏 Players</a>
        <a href="record_auction.php">💰 Auction Records</a>
        <a class="selected" href="reports.php">📊 Reports</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="container-wide">
            <h1>📊 Auction Reports</h1>

            <!-- REVENUE SUMMARY -->
            <div class="table-card">
                <h3>💰 Revenue Summary</h3>
                <table>
                    <tbody>
                        <tr>
                            <th>🎪 Total Players Sold</th>
                            <td><?php echo $revenue_summary['total_sold'] ?? 0; ?></td>
                        </tr>
                        <tr>
                            <th>❌ Total Players Unsold</th>
                            <td><?php echo $revenue_summary['total_unsold'] ?? 0; ?></td>
                        </tr>
                        <tr>
                            <th>💵 Total Revenue</th>
                            <td>₹<?php echo number_format((int) ($revenue_summary['total_revenue'] ?? 0)); ?></td>
                        </tr>
                        <tr>
                            <th>📊 Average Selling Price</th>
                            <td>₹<?php echo number_format((int) ($revenue_summary['avg_price'] ?? 0)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- AUCTION RECORDS REPORT -->
            <div class="table-card">
                <h3>📋 Auction Records Report</h3>
                <table>
                    <thead>
                        <tr>
                            <th>🏏 Player Name</th>
                            <th>📍 Position</th>
                            <th>🏷️ Team Name</th>
                            <th>💰 Final Price</th>
                            <th>✅ Result</th>
                            <th>⏰ Sold Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = mysqli_fetch_assoc($auction_records)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['player_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($record['position'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($record['team_name'] ?? '-'); ?></td>
                            <td>₹<?php echo number_format((int) ($record['final_price'] ?? 0)); ?></td>
                            <td><?php echo htmlspecialchars($record['result'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($record['sold_time'] ?? '-'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- TEAMS REPORT -->
            <div class="table-card">
                <h3>👥 Teams Report</h3>
                <table>
                    <thead>
                        <tr>
                            <th>🏷️ Team Name</th>
                            <th>👔 Manager</th>
                            <th>🏅 Achievements</th>
                            <th>🎪 Players Sold</th>
                            <th>💸 Total Spent</th>
                            <th>💼 Purse Remaining</th>
                            <th>✅ Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($team = mysqli_fetch_assoc($teams)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team['team_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($team['manager_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($team['achievements'] ?? '-'); ?></td>
                            <td><?php echo $team['players_sold'] ?? 0; ?></td>
                            <td>₹<?php echo number_format((int) ($team['total_spent'] ?? 0)); ?></td>
                            <td>₹<?php echo number_format((int) ($team['purse_remaining'] ?? 0)); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($team['status'] ?? '-')); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- PLAYERS REPORT -->
            <div class="table-card">
                <h3>🏏 Players Report</h3>
                <table>
                    <thead>
                        <tr>
                            <th>🏏 Player Name</th>
                            <th>📍 Position</th>
                            <th>🎂 Age</th>
                            <th>🎯 Playing Style</th>
                            <th>🏷️ Sold To Team</th>
                            <th>💰 Selling Price</th>
                            <th>✅ Auction Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($player = mysqli_fetch_assoc($players)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player['name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($player['position'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($player['age'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($player['playing_style'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($player['team_name'] ?? '-'); ?></td>
                            <td>₹<?php echo ($player['auction_result'] === 'SOLD') ? number_format((int) $player['sold_price']) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($player['auction_result'] ?? '-'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

</body>
</html>
