<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "periodtrackerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['K_ID']) && isset($_POST['Status'])) {
    $k_id = $_POST['K_ID'];
    $status = $_POST['Status'];

    $stmt = $conn->prepare("UPDATE kits SET Status = ? WHERE K_ID = ?");
    $stmt->bind_param("si", $status, $k_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid";
}
?>
