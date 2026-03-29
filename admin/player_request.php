<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = $_SESSION['auction_id'];

// Fetch all players for this auction
$query = "SELECT * FROM player WHERE auction_id='$auction_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Player Requests</title>

    <link href="../css/style.css" rel="stylesheet">
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
        <a class="selected" href="player_request.php">🏏 Players</a>
        <a href="record_auction.php">💰 Auction Records</a>
        <a href="reports.php">📊 Reports</a>
    </div>

<div class="container">

    <h2>🏏 Player Requests</h2>
    <p>Approve or reject players</p>

    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Age</th>
                <th>Position</th>
                <th>Playing Style</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php
        if(mysqli_num_rows($result) > 0){
            $i = 1;
            while($row = mysqli_fetch_assoc($result)){
        ?>

            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['age']; ?></td>
                <td><?php echo $row['position']; ?></td>
                <td><?php echo $row['playing_style']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if(strtolower($row['status']) == 'pending'){ ?>
                        <a href="approve_player.php?id=<?php echo $row['player_id']; ?>&auction_id=<?php echo $auction_id; ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="reject_player.php?id=<?php echo $row['player_id']; ?>&auction_id=<?php echo $auction_id; ?>" class="btn btn-danger btn-sm">Reject</a>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
            </tr>

        <?php
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No Players</td></tr>";
        }
        ?>

        </tbody>
    </table>

</div>

</body>
</html>