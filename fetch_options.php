<?php
$conn = new mysqli("localhost", "root", "", "periodtrackerdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = [
    "users" => [],
    "kits" => [],
    "DeliveryPerson" => []
];

$users = $conn->query("SELECT user_id, name FROM users");
while ($row = $users->fetch_assoc()) {
    $data["users"][] = $row;
}

$kits = $conn->query("SELECT kit_id, items FROM kits");
while ($row = $kits->fetch_assoc()) {
    $data["kits"][] = $row;
}

$dps = $conn->query("SELECT dp_id, name FROM deliveryperson");
while ($row = $dps->fetch_assoc()) {
    $data["DeliveryPerson"][] = $row;
}

echo json_encode($data);
$conn->close();
?>
