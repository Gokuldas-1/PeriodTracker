<?php
// Database connection
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

// Function to get inventory items
function getInventoryItems($category) {
    global $conn;
    $sql = "SELECT * FROM inventory_items WHERE category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    return $items;
}

// Handle item update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update") {
    $itemId = $_POST["item_id"];
    $newStock = $_POST["new_stock"];
    
    $sql = "UPDATE inventory_items SET stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $newStock, $itemId);
    
    if ($stmt->execute()) {
        $response = ["success" => true, "message" => "Stock updated successfully!"];
    } else {
        $response = ["success" => false, "message" => "Error updating stock: " . $conn->error];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle item addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $category = $_POST["category"];
    
    $sql = "INSERT INTO inventory_items (name, price, stock, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdis", $name, $price, $stock, $category);
    
    if ($stmt->execute()) {
        $response = ["success" => true, "message" => "$name added successfully to $category inventory!"];
    } else {
        $response = ["success" => false, "message" => "Error adding item: " . $conn->error];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}   

// Handle item deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $itemId = $_POST["item_id"];
    
    $sql = "DELETE FROM inventory_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    
    if ($stmt->execute()) {
        $response = ["success" => true, "message" => "Item deleted successfully!"];
    } else {
        $response = ["success" => false, "message" => "Error deleting item: " . $conn->error];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get inventory data for display
$basicItems = getInventoryItems("basic");
$premiumItems = getInventoryItems("premium");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        body {
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
        
        .back-btn {
            position: absolute;
            top: 15px;
            left: 20px;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .back-btn:hover {
            background: #3e8e41;
        }
        
        .inventory-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .category {
            margin-bottom: 30px;
        }
        
        .category h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .stock-low {
            color: red;
            font-weight: bold;
        }
        
        .stock-ok {
            color: orange;
        }
        
        .stock-good {
            color: green;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .update-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .update-btn:hover {
            background-color: #3e8e41;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        
        .add-item {
            margin-top: 20px;
            text-align: right;
        }
        
        .add-btn {
            background-color: #2196F3;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
        }
        
        .add-btn:hover {
            background-color: #0b7dda;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .submit-btn:hover {
            background-color: #3e8e41;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <header>
        <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <h1>Inventory Management</h1>
    </header>
    
    <div class="inventory-container">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert <?php echo ($_GET['status'] == 'success') ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
    
        <div class="category">
            <h2>Basic Kit Items</h2>
            <table id="basic-items">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price (₹)</th>
                        <th>Available Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($basicItems as $index => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['stock']; ?></td>
                            <td class="<?php 
                                if ($item['stock'] < 30) echo 'stock-low';
                                elseif ($item['stock'] < 75) echo 'stock-ok';
                                else echo 'stock-good';
                            ?>">
                                <?php 
                                    if ($item['stock'] < 30) echo 'Low';
                                    elseif ($item['stock'] < 75) echo 'Moderate';
                                    else echo 'Good';
                                ?>
                            </td>
                            <td class="actions">
                                <button class="update-btn" onclick="openUpdateModal(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>', <?php echo $item['stock']; ?>)">Update Stock</button>
                                <button class="delete-btn" onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="add-item">
                <button class="add-btn" onclick="openAddModal('basic')">Add New Basic Item</button>
            </div>
        </div>
        
        <div class="category">
            <h2>Premium Kit Items</h2>
            <table id="premium-items">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price (₹)</th>
                        <th>Available Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($premiumItems as $index => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['stock']; ?></td>
                            <td class="<?php 
                                if ($item['stock'] < 30) echo 'stock-low';
                                elseif ($item['stock'] < 75) echo 'stock-ok';
                                else echo 'stock-good';
                            ?>">
                                <?php 
                                    if ($item['stock'] < 30) echo 'Low';
                                    elseif ($item['stock'] < 75) echo 'Moderate';
                                    else echo 'Good';
                                ?>
                            </td>
                            <td class="actions">
                                <button class="update-btn" onclick="openUpdateModal(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>', <?php echo $item['stock']; ?>)">Update Stock</button>
                                <button class="delete-btn" onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="add-item">
                <button class="add-btn" onclick="openAddModal('premium')">Add New Premium Item</button>
            </div>
        </div>
    </div>
    
    <!-- Update Stock Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Stock</h2>
            <form id="updateStockForm">
                <div class="form-group">
                    <label for="item-name">Item Name:</label>
                    <input type="text" id="item-name" readonly>
                </div>
                <div class="form-group">
                    <label for="current-stock">Current Stock:</label>
                    <input type="number" id="current-stock" readonly>
                </div>
                <div class="form-group">
                    <label for="new-stock">New Stock:</label>
                    <input type="number" id="new-stock" required min="0">
                </div>
                <input type="hidden" id="item-id">
                <input type="hidden" name="action" value="update">
                <button type="submit" class="submit-btn">Update Stock</button>
            </form>
        </div>
    </div>
    
    <!-- Add Item Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Item</h2>
            <form id="addItemForm">
                <div class="form-group">
                    <label for="new-item-name">Item Name:</label>
                    <input type="text" id="new-item-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="new-item-price">Price (₹):</label>
                    <input type="number" id="new-item-price" step="0.01" min="0" name="price" required>
                </div>
                <div class="form-group">
                    <label for="new-item-stock">Initial Stock:</label>
                    <input type="number" id="new-item-stock" min="0" name="stock" required>
                </div>
                <input type="hidden" id="new-item-category">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="submit-btn">Add Item</button>
            </form>
        </div>
    </div>
    
    <script>
        // DOM Elements
        const updateModal = document.getElementById("updateModal");
        const addModal = document.getElementById("addModal");
        const updateStockForm = document.getElementById("updateStockForm");
        const addItemForm = document.getElementById("addItemForm");
        const closeButtons = document.querySelectorAll(".close");
        
        // Initialize event listeners
        document.addEventListener("DOMContentLoaded", function() {
            // Close modal when clicking on X
            closeButtons.forEach(btn => {
                btn.addEventListener("click", function() {
                    updateModal.style.display = "none";
                    addModal.style.display = "none";
                });
            });
            
            // Close modal when clicking outside of it
            window.addEventListener("click", function(event) {
                if (event.target === updateModal) {
                    updateModal.style.display = "none";
                }
                if (event.target === addModal) {
                    addModal.style.display = "none";
                }
            });
            
            // Handle update stock form submission
            updateStockForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const itemId = document.getElementById("item-id").value;
                const newStock = document.getElementById("new-stock").value;
                
                // AJAX request to update stock
                const formData = new FormData();
                formData.append("item_id", itemId);
                formData.append("new_stock", newStock);
                formData.append("action", "update");
                
                fetch("inventory_management.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating stock.");
                });
                
                updateModal.style.display = "none";
            });
            
            // Handle add item form submission
            addItemForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const category = document.getElementById("new-item-category").value;
                const name = document.getElementById("new-item-name").value;
                const price = document.getElementById("new-item-price").value;
                const stock = document.getElementById("new-item-stock").value;
                
                // AJAX request to add item
                const formData = new FormData();
                formData.append("name", name);
                formData.append("price", price);
                formData.append("stock", stock);
                formData.append("category", category);
                formData.append("action", "add");
                
                fetch("inventory_management.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while adding the item.");
                });
                
                addModal.style.display = "none";
            });
        });
        
        // Function to open update modal
        function openUpdateModal(itemId, itemName, currentStock) {
            document.getElementById("item-id").value = itemId;
            document.getElementById("item-name").value = itemName;
            document.getElementById("current-stock").value = currentStock;
            document.getElementById("new-stock").value = currentStock;
            
            updateModal.style.display = "block";
        }
        
        // Function to open add modal
        function openAddModal(category) {
            document.getElementById("new-item-name").value = "";
            document.getElementById("new-item-price").value = "";
            document.getElementById("new-item-stock").value = "";
            document.getElementById("new-item-category").value = category;
            
            addModal.style.display = "block";
        }
        
        // Function to delete item
        function deleteItem(itemId, itemName) {
            if (confirm(`Are you sure you want to delete ${itemName} from inventory?`)) {
                // AJAX request to delete item
                const formData = new FormData();
                formData.append("item_id", itemId);
                formData.append("action", "delete");
                
                fetch("inventory_management.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while deleting the item.");
                });
            }
        }
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>