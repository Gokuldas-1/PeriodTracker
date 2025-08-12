<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "periodtrackerdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $password = md5($_POST["password"]); // secure hashing

    $check_email = "SELECT * FROM user WHERE email=? OR phone=?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "⚠️ User already registered with this email or phone!";
    } else {
        $sql = "INSERT INTO user (name, phone, email, address, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $phone, $email, $address, $password);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: user_login.html");
            exit();
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <style>
        body {
            background-color: palevioletred;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 40px;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 26px;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            font-weight: bold;
            margin-bottom: 6px;
            display: block;
            font-size: 15px;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        .submit-btn {
            width: 100%;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 14px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 15px;
            font-size: 16px;
            color: red;
        }
        p {
            margin-top: 15px;
            font-size: 14px;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>User Registration</h2>
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" placeholder="Enter Name" required>
        </div>
        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="phone" placeholder="Enter Phone Number" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter Email" required>
        </div>
        <div class="form-group">
            <label>Address:</label>
            <textarea name="address" placeholder="Enter Address" required></textarea>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter Password" required>
        </div>
        <button type="submit" class="submit-btn">Register</button>
    </form>
    <p>Already have an account? <a href="user_login.html">Login here</a></p>
</div>
</body>
</html>
