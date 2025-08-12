<?php
$servername = "localhost";
$username = "root";  // Change as needed
$password = "";      // Change as needed
$dbname = "periodtrackerdb"; // Your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$delivery_id = $_POST['delivery_id'];
$status = $_POST['status'];

// Update the status in the database
$sql = "UPDATE order_details SET STATUS = '$status' WHERE K_ID = '$delivery_id'";

if ($conn->query($sql) === TRUE) {
    echo "UPDATE order_details SET STATUS = '$status' WHERE ORDER_ID = '$delivery_id'";
} else {
    echo "⚠️ Error: " . $conn->error;
}

$conn->close();
?>
