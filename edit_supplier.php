<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'inventory_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM suppliers WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
    } else {
        die("Supplier not found.");
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $contact_details = $conn->real_escape_string($_POST['contact_details']);
    $email = $conn->real_escape_string($_POST['email']);
    $pricing_info = $conn->real_escape_string($_POST['pricing_info']);
    $description = $conn->real_escape_string($_POST['description']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $contact_details)) {
        die("Invalid contact number.");
    }

    $sql = "UPDATE suppliers SET name='$name', contact_details='$contact_details', email='$email', pricing_info='$pricing_info', description='$description' WHERE id='$id'";

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Edit Supplier</h1>
        <form action="edit_supplier.php" method="POST" class="mt-4">
            <input type="hidden" name="id" value="<?php echo $supplier['id']; ?>">
            <div class="form-group">
                <label for="name">Supplier Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $supplier['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_details">Contact Details:</label>
                <input type="text" class="form-control" id="contact_details" name="contact_details" value="<?php echo
