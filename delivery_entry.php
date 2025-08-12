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
$did = $_POST['did'];
$uid = $_POST['uid'];
$kid = $_POST['kid'];
$dpid = $_POST['dpid'];

// Check if UID exists in user table
$checkUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
$checkUser->bind_param("s", $uid);
$checkUser->execute();
$checkUser->store_result();
if ($checkUser->num_rows == 0) {
    echo "⚠️ Error: User ID (UID) does not exist!";
    exit();
}

// Check if KID exists in kit table
$checkKit = $conn->prepare("SELECT id FROM kits WHERE id = ?");
$checkKit->bind_param("s", $kid);
$checkKit->execute();
$checkKit->store_result();
if ($checkKit->num_rows == 0) {
    echo "⚠️ Error: Kit ID (KID) does not exist!";
    exit();
}

// Check if DPID exists in DeliveryPerson table
$checkDeliveryPerson = $conn->prepare("SELECT id FROM deliveryperson WHERE id = ?");
$checkDeliveryPerson->bind_param("s", $dpid);
$checkDeliveryPerson->execute();
$checkDeliveryPerson->store_result();
if ($checkDeliveryPerson->num_rows == 0) {
    echo "⚠️ Error: Delivery Person ID (DPID) does not exist!";
    exit();
}

// Prepare SQL statement to prevent SQL Injection
$stmt = $conn->prepare("INSERT INTO deliveries (did, uid, kid, dpid) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $did, $uid, $kid, $dpid);

// Execute and check if successful
if ($stmt->execute()) {
    echo "✅ Successfully Submitted!";
} else {
    echo "⚠️ Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
