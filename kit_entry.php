<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "kit_management"; // Your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$kid = $_POST['kid'];
$quantity = $_POST['quantity'];
$type = $_POST['type'];

// Validate that quantity is a positive integer
if (!is_numeric($quantity) || $quantity <= 0) {
    echo "⚠️ Error: Quantity must be a positive number!";
    exit();
}

// Prepare SQL statement to prevent SQL Injection
$stmt = $conn->prepare("INSERT INTO kits (kid, quantity, type) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $kid, $quantity, $type);

// Execute and check if successful
if ($stmt->execute()) {
    echo "✅ Kit Entry Successfully Submitted!";
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
