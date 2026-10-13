<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "THE_AUCTION"; // Updated to match your name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>