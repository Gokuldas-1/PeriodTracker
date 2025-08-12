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
$user_id = $_SESSION['U_ID'];
$last_period_date = $_POST['last_period_date'];
$avg_cycle = $_POST['avg_cycle'];
$feedback = $_POST['feedback'];
echo  "this is last". $last_period_date;

// Prepare SQL statement to prevent SQL Injection
$stmt = $conn->prepare("INSERT INTO perioddetails (U_ID, LastPeriod, CycleLength, Feedback) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $user_id, $last_period_date, $avg_cycle, $feedback);

// Execute and check if successful
if ($stmt->execute()) {
    echo "✅ Successfully Updated!";
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
