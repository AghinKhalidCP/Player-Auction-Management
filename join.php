<?php
include("config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $auction_code = mysqli_real_escape_string($conn, $_POST['auction_code']);

    $query = "SELECT * FROM Auction WHERE auction_code = '$auction_code'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "<script>
                alert('Invalid Auction Code!');
                window.location.href='index.php';
              </script>";
        exit();
    }

    $auction = mysqli_fetch_assoc($result);
    $auction_id = $auction['auction_id'];

    $players_count = mysqli_num_rows(
        mysqli_query($conn, "SELECT * FROM Player WHERE auction_id = $auction_id")
    );

    $teams_count = mysqli_num_rows(
        mysqli_query($conn, "SELECT * FROM Team WHERE auction_id = $auction_id")
    );

    $sold_query = "
        SELECT Player.name, Team.team_name, Auction_Record.final_price
        FROM Auction_Record
        JOIN Player ON Auction_Record.player_id = Player.player_id
        LEFT JOIN Team ON Auction_Record.team_id = Team.team_id
        WHERE Auction_Record.auction_id = $auction_id
        AND Auction_Record.result = 'SOLD'
    ";

    $sold_result = mysqli_query($conn, $sold_query);

} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auction Summary</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container-wide">

    <h1 class="main-title"><?php echo $auction['auction_name']; ?></h1>

    <!-- Auction Summary Cards -->
    <div class="stats-row">

        <div class="stat-card">
            <h2><?php echo $auction['auction_date']; ?></h2>
            <p>Auction Date</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $auction['venue']; ?></h2>
            <p>Venue</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $players_count; ?></h2>
            <p>Total Players</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $teams_count; ?></h2>
            <p>Total Teams</p>
        </div>

    </div>

    <!-- Filter Controls -->
    <div class="filter-bar">
        <select id="teamFilter" onchange="filterTable()">
            <option value="">Filter by Team</option>
            <?php
            $team_list = mysqli_query($conn, "SELECT team_name FROM Team WHERE auction_id=$auction_id");
            while($t = mysqli_fetch_assoc($team_list)){
                echo "<option value='".$t['team_name']."'>".$t['team_name']."</option>";
            }
            ?>
        </select>

        <select id="priceSort" onchange="sortTable()">
            <option value="">Sort by Price</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
        </select>
    </div>

    <!-- Sold Players Table -->
    <div class="table-card">
        <h2>Sold Players</h2>

        <table id="auctionTable">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Team</th>
                    <th>Final Price</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($sold_result)){ ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['team_name']; ?></td>
                    <td><?php echo $row['final_price']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Continue As Section -->
    <div class="continue-section">
        <h2>Continue As</h2>

        <div class="role-cards">
            <a href="admin/login.php?code=<?php echo $auction_code; ?>" class="role-card admin">
                <h3>Admin</h3>
                <p>Manage Auction</p>
            </a>

            <a href="team/login.php?code=<?php echo $auction_code; ?>" class="role-card team">
                <h3>Team Manager</h3>
                <p>Manage Team</p>
            </a>

            <a href="player/login.php?code=<?php echo $auction_code; ?>" class="role-card player">
                <h3>Player</h3>
                <p>View & Update Profile</p>
            </a>
        </div>
    </div>

</div>

<script>
function filterTable() {
    var team = document.getElementById("teamFilter").value;
    var rows = document.querySelectorAll("#auctionTable tbody tr");

    rows.forEach(row => {
        if(team === "" || row.cells[1].innerText === team){
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function sortTable() {
    var table = document.getElementById("auctionTable");
    var rows = Array.from(table.rows).slice(1);
    var sortType = document.getElementById("priceSort").value;

    rows.sort((a, b) => {
        var priceA = parseFloat(a.cells[2].innerText);
        var priceB = parseFloat(b.cells[2].innerText);
        return sortType === "asc" ? priceA - priceB : priceB - priceA;
    });

    rows.forEach(row => table.appendChild(row));
}
</script>

</body>
</html>