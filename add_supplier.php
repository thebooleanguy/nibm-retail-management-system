<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'inventory_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $sql = "INSERT INTO suppliers (name, contact_details, email, pricing_info, description) VALUES ('$name', '$contact_details', '$email', '$pricing_info', '$description')";

    if ($conn->query($sql) === TRUE) {
        $message = "New supplier added successfully";
        $alert_class = "alert-success";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
        $alert_class = "alert-danger";
    }
    $conn->close();
} else {
    $message = "No POST data received.";
    $alert_class = "alert-warning";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert <?php echo $alert_class; ?>" role="alert">
            <?php echo $message; ?>
        </div>
        <a href="add_supplier.html" class="btn btn-primary">Back to Add Supplier</a>
    </div>
</body>
</html>
