<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "periodtrackerdb"; // Your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$admin_name = $_POST['adminName'];
$admin_email = $_POST['adminEmail'];
$admin_password = $_POST['adminPassword'];

// Check if email is already registered
$checkEmail = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$checkEmail->bind_param("s", $admin_email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    echo "⚠️ Email is already registered!";
    exit();
}

// Hash the password before storing
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

// Prepare SQL statement to prevent SQL Injection
$stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_name, $admin_email, $hashed_password);

// Execute and check if successful
if ($stmt->execute()) {
    echo "✅ Registration successful! Redirecting to login...";
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
