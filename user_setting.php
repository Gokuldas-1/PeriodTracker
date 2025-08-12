<?php
session_start();

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "periodtrackerdb";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get session UID (fallback to 1 only for testing)
$uid = $_SESSION['U_ID'] ?? 1;

// Fetch user data
$user = null;
$stmt = $conn->prepare("SELECT * FROM user WHERE U_ID = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
$user=array();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <style>
        body { background-color: papayawhip; }
        .settings-container {
            width: 50%; margin: auto; padding: 20px; background-color: pink;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 5px; text-align: center;
        }
        label { display: block; font-weight: bold; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input:disabled { background-color: #f0f0f0; cursor: not-allowed; }
        button { margin-top: 15px; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="settings-container">
        <h2>Update Profile</h2>

        <?php if ($user): ?>
        <form id="settingsForm">
            <label for="name">Name:</label>
            <input type="text" id="name" disabled value="<?= htmlspecialchars($user['name']) ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" disabled value="<?= htmlspecialchars($user['email']) ?>">

            <label for="contact">Phone:</label>
            <input type="text" id="contact" disabled value="<?= htmlspecialchars($user['phone']) ?>">

            <label for="address">New Address:</label>
            <input type="text" id="address" name="address" required value="<?= htmlspecialchars($user['address']) ?>">

            <label for="old-password">Old Password:</label>
            <input type="password" id="old-password" name="old-password">

            <label for="new-password">New Password:</label>
            <input type="password" id="new-password" name="new-password">

            <button type="submit">Update</button>
        </form>
        <p id="message"></p>
        <?php else: ?>
            <p style="color: red;">User not found. Please log in again.</p>
        <?php endif; ?>
    </div>

    <script>
    document.getElementById("settingsForm")?.addEventListener("submit", function(e) {
        e.preventDefault();
        const address = document.getElementById("address").value;
        const oldPassword = document.getElementById("old-password").value;
        const newPassword = document.getElementById("new-password").value;

        const formData = new FormData();
        formData.append("address", address);
        formData.append("oldPassword", oldPassword);
        formData.append("newPassword", newPassword);

        fetch("user_setting_update.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById("message").textContent = data;
            document.getElementById("old-password").value = "";
            document.getElementById("new-password").value = "";

            if (data.includes("successfully")) {
                document.getElementById("message").style.color = "green";
                setTimeout(() => {
                    window.location.href = "user_dashboard.html";
                }, 1500);
            } else {
                document.getElementById("message").style.color = "red";
            }
        });
    });
    </script>
</body>
</html>
