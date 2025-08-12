<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "periodtrackerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Determine the action based on request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch deliveries (GET request)
    fetchDeliveries($conn);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update status (POST request)
    updateStatus($conn);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

// Function to fetch all deliveries
function fetchDeliveries($conn) {
    // Query to get all delivery data
    $sql = "SELECT o.ORDER_ID, k.TYPE, u.Address, o.STATUS FROM order_details o LEFT JOIN kits k on k.K_ID=o.K_ID LEFT JOIN user u on k.U_ID=u.U_ID;";
    $result = $conn->query($sql);

    $deliveries = [];

    if ($result->num_rows > 0) {
        // Fetch data and store in array
        while($row = $result->fetch_assoc()) {
            $deliveries[] = [
                "delivery_id" => $row["ORDER_ID"],
                "kit_items" => $row["TYPE"],
                "address" => $row["Address"],
                "status" => $row["STATUS"]
            ];
        }
    }

    // Set content type to JSON
    header('Content-Type: application/json');
    
    // Return data as JSON
    echo json_encode($deliveries);
}

// Function to update delivery status
function updateStatus($conn) {
    // Get POST data
    $delivery_id = isset($_POST['delivery_id']) ? $_POST['delivery_id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;

    // Validate input
    if (!$delivery_id || !$status) {
        echo "Error: Missing delivery ID or status";
        exit;
    }

    // Sanitize input to prevent SQL injection
    $delivery_id = $conn->real_escape_string($delivery_id);
    $status = $conn->real_escape_string($status);

    // Update the status in the database
    $sql = "UPDATE order_details SET STATUS = '$status' WHERE ORDER_ID = '$delivery_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>