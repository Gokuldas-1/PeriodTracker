<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "periodtrackerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Hash the input password using MD5 (to match DB)
    $hashedPassword = md5($password);

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user found, start session
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION["user"] = $email;
        $_SESSION["U_ID"] = $row['U_ID'];
        echo "Login successful!"; // Signal frontend to redirect
    } else {
        echo "Invalid email or password!";
    }

    $stmt->close();
}

$conn->close();
?>
