<?php
$conn = new mysqli('localhost', 'root', '', 'inventory_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = 'testuser';
$password = password_hash('password123', PASSWORD_DEFAULT);
$role = 'Owner';

$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

if ($conn->query($sql) === TRUE) {
    echo "New user created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>