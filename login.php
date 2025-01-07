<?php
// Start a session
session_start();

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "ticket";

// Create connection
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $upswd1 = trim($_POST['Password']);

    // Validate inputs
    if (empty($email) || empty($upswd1)) {
        echo '<script>alert("Email and password are required."); window.location.href="index.html"; window.history.back();</script>';
    } else {
        // Prepare and execute SQL query
        $stmt = $conn->prepare("SELECT email, upswd1 FROM register WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the password
            if ($upswd1 === $row['upswd1']) {
                // Login successful
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $email;

                echo '<script>alert("Login successful!"); window.location.href="index2.html";</script>';
                exit();
            } else {
                echo '<script>alert("Invalid password."); window.location.href="index.html"; window.history.back();</script>';
            }
        } else {
            echo '<script>alert("No user found with that email."); window.location.href="index.html"; window.history.back();</script>';
        }

        $stmt->close();
    }
}
$conn->close();
?>
