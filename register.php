<?php

$uname1 = $_POST['uname1'];
$email  = $_POST['email'];
$upswd1 = $_POST['upswd1'];
$upswd2 = $_POST['upswd2'];

if (!empty($uname1) && !empty($email) && !empty($upswd1) && !empty($upswd2)) {

    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "ticket";

    // Create connection
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    } else {
        // Function to validate password strength
        function isStrongPassword($password) {
            $length = strlen($password) >= 8; // Minimum 8 characters
            $uppercase = preg_match('@[A-Z]@', $password); // At least one uppercase letter
            $lowercase = preg_match('@[a-z]@', $password); // At least one lowercase letter
            $number = preg_match('@[0-9]@', $password); // At least one number
            $specialChar = preg_match('@[^\w]@', $password); // At least one special character

            return $length && $uppercase && $lowercase && $number && $specialChar;
        }

        // Check password strength
        if (!isStrongPassword($upswd1)) {
            echo '<script>alert("Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character."); window.location.href="register.html"; window.history.back();</script>';
            die();
        }

        // Check if passwords match
        if ($upswd1 !== $upswd2) {
            echo '<script>alert("Passwords do not match."); window.location.href="register.html"; window.history.back();</script>';
            die();
        }

        // Check if email is already registered
        $SELECT = "SELECT email FROM register WHERE email = ? LIMIT 1";
        $INSERT = "INSERT INTO register (uname1, email, upswd1, upswd2) VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->store_result();
        $rnum = $stmt->num_rows;

        if ($rnum == 0) {
            $stmt->close();
            $stmt = $conn->prepare($INSERT);
            $stmt->bind_param("ssss", $uname1, $email, $upswd1, $upswd2);
            $stmt->execute();
            echo '<script>alert("New record inserted successfully."); window.location.href="index.html";</script>';
        } else {
            echo '<script>alert("Someone already registered using this email."); window.location.href="register.html"; window.history.back();</script>';
        }

        $stmt->close();
        $conn->close();
    }
} else {
    echo '<script>alert("All fields are required."); window.location.href="register.html"; window.history.back();</script>';
    die();
}
?>
