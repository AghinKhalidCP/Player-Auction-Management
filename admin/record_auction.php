<?php
session_start();
include("../config/db.php");

// Check login
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$auction_id = intval($_SESSION['auction_id']);

// Handle AJAX player details request
if(isset($_GET['get_player']) && $_GET['get_player'] == 1){
    header('Content-Type: application/json');
    $player_id = intval($_GET['player_id']);
    $result = mysqli_query($conn, "SELECT player_id, name, age, position, playing_style, email, phone FROM Player WHERE player_id=$player_id AND auction_id=$auction_id");
    
    if($result && ($row = mysqli_fetch_assoc($result))){
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
    exit();
}

// Fetch players (only with NULL team_id - not yet auctioned)
$players = mysqli_query($conn, "
    SELECT * FROM Player 
    WHERE auction_id=$auction_id AND LOWER(status)='approved' AND team_id IS NULL
    ORDER BY RAND()
");

// Fetch teams
$teams = mysqli_query($conn, "
    SELECT * FROM Team 
    WHERE auction_id=$auction_id AND LOWER(status)='approved'
");

if(isset($_POST['submit'])){

    $player_id = intval($_POST['player_id']);
    $team_id = intval($_POST['team_id']);
    $result = mysqli_real_escape_string($conn, $_POST['result']);
    $price = (!empty($_POST['price'])) ? intval($_POST['price']) : 0;

    // Validate: SOLD records must have a price
    if($result == "SOLD" && $price <= 0){
        echo "<script>alert('Please enter a valid price for SOLD players'); window.history.back();</script>";
        exit();
    }

    // Insert auction record
    $insert = "INSERT INTO Auction_Record 
    (auction_id, player_id, team_id, final_price, result)
    VALUES 
    ($auction_id, $player_id, $team_id, $price, '$result')";

    mysqli_query($conn, $insert);

    // If SOLD → update team purse + player status and store team_id
    if($result == "SOLD"){

        // Reduce purse from team's purse_remaining
        mysqli_query($conn, "
            UPDATE Team 
            SET purse_remaining = purse_remaining - $price
            WHERE team_id = $team_id
        ");

        // Assign player to team and store team_id in player table (status remains Approved)
        mysqli_query($conn, "
            UPDATE Player 
            SET team_id=$team_id
            WHERE player_id=$player_id
        ");
    } else {

        // Mark as unsold by setting team_id=0 (status remains Approved)
        mysqli_query($conn, "
            UPDATE Player 
            SET team_id=0
            WHERE player_id=$player_id
        ");
    }

    echo "<script>alert('Auction Recorded Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Auction</title>
    <link rel="stylesheet" href="../css/style.css">
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
        <a class="selected" href="record_auction.php">💰 Auction Records</a>
        <a href="reports.php">📊 Reports</a>
    </div>

<div class="container">

    <h2>💰 Record Auction</h2>
    <p>Record player sales during the auction</p>

    <div class="auction-form-row" style="margin-top: 30px;">
        <div class="table-card" style="width: 100%; max-width: 900px;">
        <form method="POST">

            <select name="player_id" id="playerSelect" onchange="loadPlayerDetails()" required>
                <option value="">Select Player</option>
                <?php 
                $players_array = [];
                while($p = mysqli_fetch_assoc($players)){ 
                    $players_array[] = $p;
                ?>
                    <option value="<?php echo intval($p['player_id']); ?>">
                        <?php echo htmlspecialchars($p['name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="button" onclick="selectRandomPlayer()" class="btn btn-success" style="margin-top: 10px;">🎲 Random Player</button>

            <select name="team_id" required style="margin-top: 20px;">
                <option value="">Select Team</option>
                <?php 
                mysqli_data_seek($teams, 0);
                while($t = mysqli_fetch_assoc($teams)){ 
                ?>
                    <option value="<?php echo intval($t['team_id']); ?>">
                        <?php echo htmlspecialchars($t['team_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <select name="result" id="resultSelect" onchange="togglePriceField()" required style="margin-top: 20px;">
                <option value="">-- Select Result --</option>
                <option value="SOLD">✅ SOLD</option>
                <option value="UNSOLD">❌ UNSOLD</option>
            </select>

            <input type="number" name="price" id="priceInput" placeholder="Enter Final Price" style="margin-top: 20px; display: none;">

            <button type="submit" name="submit" class="btn" style="margin-top: 20px; width: 100%;">Submit Record</button>

        </form>
        </div>

        <!-- PLAYER DETAILS TABLE -->
        <div id="playerDetailsTable" style="display: none; width: 100%; max-width: 900px;">
            <h3>Player Details</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td id="detailName">-</td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td id="detailAge">-</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td id="detailPosition">-</td>
                </tr>
                <tr>
                    <th>Playing Style</th>
                    <td id="detailStyle">-</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td id="detailEmail">-</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td id="detailPhone">-</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>

</div>

</div>

<script>
function selectRandomPlayer(){
    var select = document.getElementById('playerSelect');
    var options = select.querySelectorAll('option');
    var validOptions = [];
    
    for(var i = 1; i < options.length; i++){
        validOptions.push(options[i]);
    }
    
    if(validOptions.length > 0){
        var randomOption = validOptions[Math.floor(Math.random() * validOptions.length)];
        select.value = randomOption.value;
        loadPlayerDetails();
    }
}

function togglePriceField(){
    var resultSelect = document.getElementById('resultSelect');
    var priceInput = document.getElementById('priceInput');
    
    if(resultSelect.value == 'SOLD'){
        priceInput.style.display = 'block';
        priceInput.required = true;
    } else {
        priceInput.style.display = 'none';
        priceInput.required = false;
        priceInput.value = '0';
    }
}

function loadPlayerDetails(){
    var playerId = document.getElementById('playerSelect').value;
    var tableDiv = document.getElementById('playerDetailsTable');
    
    if(!playerId){
        tableDiv.style.display = 'none';
        return;
    }
    
    fetch('record_auction.php?get_player=1&player_id=' + playerId)
        .then(response => response.json())
        .then(data => {
            if(data){
                document.getElementById('detailName').innerHTML = data.name || '-';
                document.getElementById('detailAge').innerHTML = data.age || '-';
                document.getElementById('detailPosition').innerHTML = data.position || '-';
                document.getElementById('detailStyle').innerHTML = data.playing_style || '-';
                document.getElementById('detailEmail').innerHTML = data.email || '-';
                document.getElementById('detailPhone').innerHTML = data.phone || '-';
                tableDiv.style.display = 'block';
            } else {
                tableDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.log('Error:', error);
            tableDiv.style.display = 'none';
        });
}
</script>

</body>
</html>
