<?php
session_start();
include 'dbconfig.php';

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the username already exists
    $checkUserSql = "SELECT * FROM login WHERE username='$username'";
    $checkUserResult = $conn->query($checkUserSql);

    if ($checkUserResult->num_rows > 0) {
        // Username already exists
        $error = "Username already taken";
        echo '<script>alert("Username already taken");</script>';
    } else {
        // Insert new user into the database
        $insertSql = "INSERT INTO login (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($insertSql) === TRUE) {
            // Registration succeeded
            $_SESSION['agent_id'] = $conn->insert_id;
            header('Location: welcome.php'); // Redirect to a welcome or login page
            exit();
        } else {
            // Registration failed
            $error = "Error: " . $insertSql . "<br>" . $conn->error;
            echo '<script>alert("Error: ' . $error . '");</script>';
        }
    }
}
?>
