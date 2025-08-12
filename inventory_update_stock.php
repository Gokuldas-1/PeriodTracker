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

if(!isset($_POST['itemid'])&&!isset($_POST['quantity'])){
    echo "Post request are not specified";
    exit();
}

// Retrieve form data
$itemid = $_POST['itemid'];
$quantity = $_POST['quantity'];

// Prepare SQL statement to insert kit selection
$stmt = $conn->prepare("UPDATE inventory_items SET stocks=? where=?");
$stmt->bind_param("ii",$quantity,$itemid);

// Execute and check if successful
if ($stmt->execute()) {
    echo "Stock Updated";
    header("Location: inventory_management.html");
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
