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

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="loginstyle.css">
    <script src="login.js"></script>
</head>

<body>
    <header>
        <div class="logo">
            <img src="logo.png">
        </div>
        <nav>
            <ul>
                <li><a href="home.html">HOME</a></li>
                <li><a href=about.html>ABOUT US</a></li>
                <li><a href=login.html>LOG IN</a></li>
                <li><a href="reviews.html">REVIEWS</a></li>
                <li><a href=buy.html>BUY</a></li>

            </ul>
        </nav>
    </header>
    <main>
        <center>
            <h1>Login</h1>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
                <br><br>
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <br><br>
                <input type="submit" onclick="return validatelogin()" id="login" value="Login">
            </form>
        </center>
    </main>
</body>

</html>