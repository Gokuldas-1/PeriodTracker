<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            background-image: url("admin_dashboard1.avif");
            background-size: cover; /* Adjusts the image to fit the screen */
            background-position: center;
            background-repeat: no-repeat; 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
         
        header {
            background: #333;
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background: red;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: darkred;
        }
        
        nav {
            background: #444;
            padding: 10px;
            text-align: center;
        }
        
        nav ul {
            list-style: none;
            padding: 0;
        }
        
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        
        .dashboard-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 50px;
        }
        
        .option {
            background: rgb(185, 216, 251);
            padding: 20px;
            margin: 20px;
            width: 300px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: blue;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .btn:hover {
            background: darkblue;
        }
        
        /* Logout Section Styling */
        .logout-section {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .logout-btn-main {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .logout-btn-main:hover {
            background: darkred;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>
    
    <main>
        <section class="dashboard-options">
            <div class="option">
                <h2>Inventory Management</h2>
                <p>Track and manage inventory levels.</p>
                <a href="inventory_management.php" class="btn">Go to Inventory Management</a>
            </div>
            <div class="option">
                <h2>Order Management</h2>
                <p>Update orders and assign deliveries.</p>
                <a href="order_management.php" class="btn">Go to Order Management</a>
            </div>
            <div class="option">
                <h2>Delivery Person Registration</h2>
                <p>Register and manage delivery personnel.</p>
                <a href="delivery_person_register.html" class="btn">Go to Registration</a>
            </div>
        </section>

        <!-- Additional Logout Button -->
        <div class="logout-section">
            <button id="logoutBtn" class="logout-btn-main">Logout</button>
        </div>
    </main>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Admin Dashboard Loaded");

            // Select both logout buttons
            const logoutBtns = document.querySelectorAll(".logout-btn, .logout-btn-main");
            
            logoutBtns.forEach(btn => {
                btn.addEventListener("click", function(event) {
                    event.preventDefault();
                    if (confirm("Are you sure you want to logout?")) {
                        window.location.href = "index.html";
                    }
                });
            });
        });
    </script>
</body>
</html>