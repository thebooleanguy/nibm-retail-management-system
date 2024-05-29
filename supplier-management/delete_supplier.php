<?php
$conn = new mysqli('localhost', 'root', '', 'inventory_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "DELETE FROM suppliers WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: view_suppliers.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    die("Invalid request.");
}

$conn->close();
?>
