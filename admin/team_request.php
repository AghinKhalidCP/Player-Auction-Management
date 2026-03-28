<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = $_SESSION['auction_id'];

// Fetch all teams for this auction
$query = "SELECT * FROM team WHERE auction_id='$auction_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Requests</title>

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
        <a class="selected" href="team_request.php">👥 Teams</a>
        <a href="player_request.php">🏏 Players</a>
        <a href="record_auction.php">💰 Auction Records</a>
        <a href="reports.php">📊 Reports</a>
    </div>

<div class="container">

    <h2>👥 Team Requests</h2>
    <p>Approve or reject teams</p>

    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Team Name</th>
                <th>Owner Name</th>
                <th>Email</th>
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
                <td><?php echo $row['team_name']; ?></td>
                <td><?php echo $row['owner_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if($row['status'] == 'pending'){ ?>
                        <a href="approve_team.php?id=<?php echo $row['team_id']; ?>&auction_id=<?php echo $auction_id; ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="reject_team.php?id=<?php echo $row['team_id']; ?>&auction_id=<?php echo $auction_id; ?>" class="btn btn-danger btn-sm">Reject</a>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
            </tr>

        <?php
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No Teams</td></tr>";
        }
        ?>

        </tbody>
    </table>

</div>

</body>
</html>