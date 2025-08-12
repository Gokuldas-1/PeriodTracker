<?php
$conn = new mysqli("localhost", "root", "", "periodtrackerdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM delivery details");
$deliveries = [];

while ($row = $result->fetch_assoc()) {
    $deliveries[] = $row;
}

echo json_encode($deliveries);
$conn->close();
?>
