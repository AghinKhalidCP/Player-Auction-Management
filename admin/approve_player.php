<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['auction_id'])){
    $player_id = $_GET['id'];
    $auction_id = $_GET['auction_id'];

    // Store approvals with consistent casing.
    $query = "UPDATE player SET status='Approved' WHERE player_id='$player_id' AND auction_id='$auction_id'";
    mysqli_query($conn, $query);
}

header("Location: player_request.php");
exit();
?>
