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

$sql = "SELECT 
    u.*, 
    k.*, 
    p.*, 
    DATE(p.LastPeriod + INTERVAL 3 DAY) AS delivery 
FROM 
    user u 
LEFT JOIN 
    kits k ON k.U_ID = u.U_ID 
LEFT JOIN 
    perioddetails p ON p.U_ID = u.U_ID;";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$sql = "SELECT * from deliveryperson";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$delivery = [];
while ($row = $result->fetch_assoc()) {
    $delivery[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Approval & Delivery Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f2f4;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            background-color: #d81b60;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .back-btn {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 15px;
            font-weight: bold;
        }
        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.5);
        }
        header h1 {
            margin: 0;
        }
        .column {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
            color: #d81b60;
        }
        .order-card {
            background-color: #f9f9f9;
            border-left: 4px solid #d81b60;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .order-card.approved {
            border-left-color: #4CAF50;
        }
        .order-card.rejected {
            border-left-color: #f44336;
        }
        .order-card.assigned {
            border-left-color: #8e24aa;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 12px;
            color: white;
            margin-right: 5px;
        }
        .badge.new {
            background-color: #2196F3;
        }
        .badge.approved {
            background-color: #4CAF50;
        }
        .badge.rejected {
            background-color: #f44336;
        }
        .badge.assigned {
            background-color: #8e24aa;
        }
        .badge.urgent {
            background-color: #ff9800;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        button {
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .approve-btn {
            background-color: #4CAF50;
            color: white;
        }
        .approve-btn:hover {
            background-color: #388E3C;
        }
        .reject-btn {
            background-color: #f44336;
            color: white;
        }
        .reject-btn:hover {
            background-color: #D32F2F;
        }
        .assign-btn {
            background-color: #8e24aa;
            color: white;
        }
        .assign-btn:hover {
            background-color: #7B1FA2;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        .close:hover {
            color: red;
        }
        .delivery-person {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
        .delivery-person:hover {
            background-color: #f3e5f5;
        }
        .delivery-person:last-child {
            border-bottom: none;
        }
        .delivery-person .status {
            margin-left: auto;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        .status.available {
            background-color: #4CAF50;
            color: white;
        }
        .status.busy {
            background-color: #f44336;
            color: white;
        }
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .filter-bar select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-bar input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .order-details {
            margin-top: 5px;
        }
        .kit-items {
            margin-top: 5px;
            padding-left: 15px;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        .empty-state p {
            margin-bottom: 15px;
        }
        .add-btn {
            background-color: #d81b60;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .add-btn:hover {
            background-color: #c2185b;
        }
        .delivery-person.selected {
            background-color: #f3e5f5;
        }
    </style>
</head>
<body >
    <div class="container">
        <header>
            <button class="back-btn" onclick="goBack()">← Back</button>
            <h1>Order Approval & Delivery Assignment</h1>
        </header>
        
         <!-- Order Approval Section -->
         <div class="column">
            <h2>New Orders Pending Approval</h2>
            <div class="filter-bar">
                <input type="text" id="searchOrders" placeholder="Search orders...">
                <select id="filterStatus">
                    <option value="all">All Orders</option>
                    <option value="new" selected>New</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="assigned">Assigned</option>
                </select>
            </div>
            
            <div id="ordersList">
                <!-- Empty state for orders -->
                <div>
                    <?php foreach($items as $i){ ?>
                        <div class='order-card <?= $i['status'] ?> ' data-id=<?= $i['K_ID']  ?>>
                        <div class="order-header">
                    <span class= "badge <?= $i['status']=='ordered'? 'new' : $i['status'] ?>" ><?= $i['status']=='ordered'? 'New' : $i['status']  ?></span>
                    <strong>Order #<?php echo $i['K_ID'] ?></strong> - <?php echo $i['name'] ?>
                </div>
                <div class="order-details">
                    <p><?php echo $i['address'] ?></p>
                    <p>Phone: <?php echo $i['phone'] ?></p>
                    <p>Expected Period: <?php echo $i['LastPeriod'] ?></p>
                    <p>Requested Delivery: <?php echo $i['delivery'] ?></p>
                    <div class="kit-items">
                        <p><?php echo $i['TYPE'] ?></p>
                    </div>
                </div>
                <div class="actions">
                    <?php  if($i['status']=='approved'){ ?>
                        <button class="assign-btn" data-id="<?= $i['K_ID'] ?>" onclick="assigndelivery(this.getAttribute('data-id'),'<?= $i['U_ID'] ?>')">Assign Delivery</button>
                        <?php  }elseif($i['status']=='rejected'){  ?> 
                            <button class="approve-btn" data-id="<?= $i['K_ID']?>" onclick="handleOrderApproval(this.getAttribute('data-id'))">Reconsider</button>

                                <?php  }elseif($i['status']=='assigned'){  ?> 
                              <?php }else{  ?>
                  
                    <button class="approve-btn" data-id="<?= $i['K_ID']?>" onclick="handleOrderApproval(this.getAttribute('data-id'))" >Approve</button>
                    <button class="reject-btn" data-id="<?= $i['K_ID']?>" onclick="openRejectionModal(this.getAttribute('data-id'))">Reject</button>
                    <?php } ?>
                    
                
                    </div>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
        
        <!-- Delivery Personnel Section -->
        <div class="column">
            <h2>Available Delivery Personnel</h2>
            <div id="deliveryPersonnelList" >
                <!-- Empty state for delivery personnel -->
                <div class="empty-state">
                   <?php foreach($delivery as $d) { ?>
                    <div class="delivery-person" data-dp-id="<?= $d['DP_ID'] ?>">
                    <div>
                    <strong><?= $d['Name'] ?></strong>
                    <p >ID: <?= $d['DP_ID'] ?> </p>
                    <p>Phone: <?= $d['Phone'] ?> </p>
                </div>
                <span class="status available">Available</span>
                   </div>
                   <?php }  ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Assignment Modal -->
    <div id="assignmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Assign Period Kit Delivery</h2>
            <p>Assign Order #<span id="assignOrderId"></span> to:</p>
            <p>User Id #<span id="assignUserId"></span></p>
            
            <div id="availableDeliveryPersons">
                <!-- Available delivery persons will be listed here when modal is opened -->
                <div class="empty-state">
                    <p>No delivery personnel available.</p>
                </div>
            </div>
            
            <div style="margin-top: 15px;">
                <label for="deliveryInstructions">Special Instructions for Delivery Person:</label>
                <textarea id="deliveryInstructions" rows="2" style="width: 100%; padding: 8px; margin-top: 5px;"></textarea>
            </div>
            
            <button id="confirmAssignment" style="margin-top: 15px; background-color: #8e24aa; color: white;">Confirm Assignment</button>
        </div>
    </div>
    
    <!-- Rejection Modal -->
    <div id="rejectionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reject Order</h2>
            <p>Please provide a reason for rejecting Order #<span id="rejectOrderId"></span>:</p>
            
            <div style="margin-top: 15px;">
                <label for="rejectionReason">Rejection Reason:</label>
                <select id="rejectionReason" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="out-of-area">Out of delivery area</option>
                    <option value="invalid-address">Invalid or incomplete address</option>
                    <option value="contact-required">Need more information from customer</option>
                    <option value="out-of-stock">Requested items out of stock</option>
                    <option value="other">Other reason</option>
                </select>
            </div>
            
            <div style="margin-top: 15px;">
                <label for="rejectionNotes">Additional Notes:</label>
                <textarea id="rejectionNotes" rows="3" style="width: 100%; padding: 8px; margin-top: 5px;"></textarea>
            </div>
            
            <button id="confirmRejection" style="margin-top: 15px; background-color: #f44336; color: white;">Confirm Rejection</button>
        </div>
    </div>
    
    <script>
        // Back button functionality
        function goBack() {
            window.history.back();
            // If there's no history to go back to, redirect to a default page
            setTimeout(function() {
                if (document.referrer === "") {
                    alert("Going back to dashboard...");
                    // In a real application, you would redirect to your dashboard
                    // window.location.href = "/dashboard";
                }
            }, 100);
        }
        
        // Modal handling
        const assignmentModal = document.getElementById('assignmentModal');
        const rejectionModal = document.getElementById('rejectionModal');
        const closeBtns = document.querySelectorAll('.close');
        
        // Close modals when close button is clicked
        closeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                assignmentModal.style.display = 'none';
                rejectionModal.style.display = 'none';
            });
        });
        
        // Close modals when clicking outside of them
        window.addEventListener('click', function(event) {
            if (event.target === assignmentModal) {
                assignmentModal.style.display = 'none';
            }
            if (event.target === rejectionModal) {
                rejectionModal.style.display = 'none';
            }
        });
        
        // Handle order rejection modal
        function openRejectionModal(orderId) {
            document.getElementById('rejectOrderId').textContent = orderId;
            rejectionModal.style.display = 'block';
        }
        
        // Handle order approval functionality
        async function handleOrderApproval(orderId) {
            let updateForm = new FormData();
            updateForm.append('K_ID', orderId);
            updateForm.append('Status', 'approved');

            try {
                const response = await fetch('update_kit_status.php', {
                    method: 'POST',
                    body: updateForm
                });
                const data = await response.text();
                console.log('Kit status updated:', data);
                alert(`Order #${orderId} has been approved!`);
                window.location.reload();
            } catch (err) {
                console.error('Update failed', err);
                alert('Failed to approve order. Please try again.');
            }
        }
        
        // Handle order assignment
        function assigndelivery(orderId, userId) {
            document.getElementById('assignOrderId').textContent = orderId;
            document.getElementById('assignUserId').textContent = userId || ''; // Handle possible undefined
            assignmentModal.style.display = 'block';
            updateAvailableDeliveryPersons();
        }
        
        // Update available delivery persons in the assignment modal
        function updateAvailableDeliveryPersons() {
            const availableDeliveryPersons = document.getElementById('availableDeliveryPersons');
            availableDeliveryPersons.innerHTML = '';
            
            // Get all available delivery persons
            const availablePersons = document.querySelectorAll('.delivery-person .status.available');
            
            if (availablePersons.length === 0) {
                availableDeliveryPersons.innerHTML = '<div class="empty-state"><p>No delivery personnel available.</p></div>';
                return;
            }
            
            availablePersons.forEach(status => {
                const person = status.closest('.delivery-person');
                const personClone = person.cloneNode(true);
                
                // Add click handler for selection
                personClone.addEventListener('click', function() {
                    // Remove selection from all persons
                    document.querySelectorAll('#availableDeliveryPersons .delivery-person').forEach(p => {
                        p.classList.remove('selected');
                        p.style.backgroundColor = '';
                    });
                    
                    // Add selection to clicked person
                    this.classList.add('selected');
                    this.style.backgroundColor = '#f3e5f5';
                });
                
                availableDeliveryPersons.appendChild(personClone);
            });
        }
        
        // Handle confirmation of rejection
        document.getElementById('confirmRejection').addEventListener('click', function() {
            const orderId = document.getElementById('rejectOrderId').textContent;
            const rejectionReason = document.getElementById('rejectionReason').value;
            const rejectionNotes = document.getElementById('rejectionNotes').value;
            
            let rejectForm = new FormData();
            rejectForm.append('K_ID', orderId);
            rejectForm.append('Status', 'rejected');
            rejectForm.append('Reason', rejectionReason);
            rejectForm.append('Notes', rejectionNotes);

            fetch('update_kit_status.php', {
                method: 'POST',
                body: rejectForm
            })
            .then(res => res.text())
            .then(data => {
                console.log('Kit status updated:', data);
                alert(`Order #${orderId} has been rejected!`);
                window.location.reload();
            })
            .catch(err => {
                console.error('Update failed', err);
                alert('Failed to reject order. Please try again.');
            });

            rejectionModal.style.display = 'none';
        });
        
        // Handle delivery assignment confirmation
        document.getElementById('confirmAssignment').addEventListener('click', function() {
            const orderId = document.getElementById('assignOrderId').textContent;
            const userId = document.getElementById('assignUserId').textContent;
            const deliveryInstructions = document.getElementById('deliveryInstructions').value;
            
            // Find selected delivery person
            let selecteddp;
            const selectedElement = document.querySelector('#availableDeliveryPersons .delivery-person.selected');
            
            if (selectedElement) {
                selecteddp = selectedElement.getAttribute('data-dp-id');
                
                // Update kit status to assigned
                let statusForm = new FormData();
                statusForm.append('K_ID', orderId);
                statusForm.append('Status', 'assigned');

                fetch('update_kit_status.php', {
                    method: 'POST',
                    body: statusForm
                })
                .then(res => res.text())
                .then(data => {
                    console.log('Kit status updated:', data);
                    
                    // Now create the assignment
                    let formData = new FormData();
                    formData.append('DP_ID', selecteddp);
                    formData.append('U_ID', userId);
                    formData.append('K_ID', orderId);
                    formData.append('Instructions', deliveryInstructions);

                    return fetch("order_assign.php", {
                        method: "POST",
                        body: formData
                    });
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Assignment result:', data);
                    alert(`Order #${orderId} has been assigned to delivery person ID: ${selecteddp}`);
                    window.location.reload();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Assignment failed. Please try again.");
                });
            } else {
                alert("Please select a delivery person before confirming.");
            }
            
            assignmentModal.style.display = 'none';
        });
        
        // Filter functionality
        document.getElementById('filterStatus').addEventListener('change', function() {
            const selectedStatus = this.value;
            
            document.querySelectorAll('.order-card').forEach(card => {
                if (selectedStatus === 'all') {
                    card.style.display = 'block';
                } else {
                    const hasStatusBadge = card.querySelector(`.badge.${selectedStatus}`);
                    card.style.display = hasStatusBadge ? 'block' : 'none';
                }
            });
        });
        
        // Search functionality
        document.getElementById('searchOrders').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            document.querySelectorAll('.order-card').forEach(card => {
                const orderText = card.textContent.toLowerCase();
                card.style.display = orderText.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>