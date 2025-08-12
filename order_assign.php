<?php
session_start();
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
    $DP_ID = $_POST['DP_ID'];
    $orderId = $_POST['U_ID'];
    $KID = $_POST['K_ID'];
    $STATUS = 'EN ROUTE';

// Prepare SQL statement to insert kit selection
$stmt = $conn->prepare("INSERT INTO order_details (DP_ID,K_ID, STATUS) VALUES (?,?,?)");
$stmt->bind_param("sss", $DP_ID, $KID, $STATUS);

// Execute and check if successful
if ($stmt->execute()) {
   
        echo "success";
   
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
