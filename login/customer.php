<?php
session_start();
include 'dbconfig.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Check if the submitted username and password match an agent record in the database
	$sql = "SELECT * FROM login WHERE username='$username' AND password='$password'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// Authentication succeeded
		$row = $result->fetch_assoc();
		$_SESSION['agent_id'] = $row['id'];
		header('Location: view.php');
		exit();
	} else {
		// Authentication failed
		$error = "Invalid username or password";
		echo '<script>alert("Invalid username or password");</script>';
	}
}

?>
