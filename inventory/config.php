<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "inventory_system";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
