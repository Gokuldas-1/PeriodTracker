<?php
$servername = "localhost";
$username = "root";  // Change if using a different database user
$password = "";      // Change if you have set a database password
$dbname = "periodtrackerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $dob = $_POST["dob"];
    $email = trim($_POST["email"]);
    $contact = trim($_POST["contact"]);
    $password = md5($_POST["password"]); // Secure password hashing

    // Check if user already exists
    $check_email = "SELECT * FROM deliveryperson WHERE Email='$email' OR Phone='$contact'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        echo "User already registered with this email, contact, or Aadhar!";
    } else {
        // Insert user data
        $sql = "INSERT INTO deliveryperson (Name, DoB, Email, Phone, Password) 
                VALUES ('$name', '$dob', '$email', '$contact', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "Registration successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
