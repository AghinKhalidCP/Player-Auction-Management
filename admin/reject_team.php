<?php
include("../config/db.php");
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['auction_id'])){
    $team_id = $_GET['id'];
    $auction_id = $_GET['auction_id'];

    // Update status to rejected
    $query = "UPDATE team SET status='rejected' WHERE team_id='$team_id' AND auction_id='$auction_id'";
    mysqli_query($conn, $query);
}

header("Location: team_request.php");
exit();
?>