<?php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "player_auction_management";
$conn = mysqli_connect($host, $user, $password, $db_name);

if(!$conn){
    die("Connection Failed".mysqli.connect.error());
}
?>