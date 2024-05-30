<?php
// Database connection (adjust as per your database settings)
$host = 'localhost';
$dbname = 'inventory_system';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Database connection error
    $response = array(
        'error' => true,
        'message' => 'Database connection failed: ' . $e->getMessage()
    );
    echo json_encode($response);
    exit;
}

// Retrieve username and password from POST request
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // Prepare SQL statement to fetch user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // If password matches, return JSON response with user details
            $response = array(
                'error' => false,
                'username' => $user['username'],
                'role' => $user['role']
            );
            echo json_encode($response);
        } else {
            // Password does not match
            $response = array(
                'error' => true,
                'message' => 'Invalid username or password. Please try again.'
            );
            echo json_encode($response);
        }
    } else {
        // User not found
        $response = array(
            'error' => true,
            'message' => 'Invalid username or password. Please try again.'
        );
        echo json_encode($response);
    }
} catch (PDOException $e) {
    // Other database errors
    $response = array(
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    );
    echo json_encode($response);
}
?>
