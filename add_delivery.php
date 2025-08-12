<?php
$conn = new mysqli("localhost", "root", "", "periodtrackerdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_POST['user_id'];
$kit_id = $_POST['kit_id'];
$dp_id = $_POST['dp_id'];

$sql = "INSERT INTO deliveries (user_id, kit_id, dp_id, status) VALUES ('$user_id', '$kit_id', '$dp_id', 'Pending')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Delivery Added Successfully!";
} else {
    echo "⚠️ Error: " . $conn->error;
}

$conn->close();
?>
