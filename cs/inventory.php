<?php
// Database connection
$host = 'localhost';
$db = 'crilyn';  // Your database name
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass);

// Add Product Functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $quantity = (int)$_POST['quantity'];
    $storage_type = $_POST['storage_type'];
    $expiration_date = $_POST['expiration_date'];
    
    // Insert new product into the inventory
    $stmt = $pdo->prepare("INSERT INTO inventory (product_name, quantity, storage_type, expiration_date, date_received)
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$product_name, $quantity, $storage_type, $expiration_date]);
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Release Product Functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['release_product'])) {
    $product_name = $_POST['product_name'];
    $release_quantity = (int)$_POST['quantity'];
    
    // Fetch the oldest stock of the product (FIFO)
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE product_name = ? ORDER BY date_received ASC");
    $stmt->execute([$product_name]);
    $inventory_items = $stmt->fetchAll();

    $remaining_quantity_to_release = $release_quantity;
    
    foreach ($inventory_items as $item) {
        $inventory_id = $item['id'];
        $inventory_quantity = (int)$item['quantity'];

        if ($remaining_quantity_to_release <= 0) {
            break;
        }
        
        if ($inventory_quantity <= $remaining_quantity_to_release) {
            // Release the entire stock of this item
            $stmt = $pdo->prepare("UPDATE inventory SET quantity = 0 WHERE id = ?");
            $stmt->execute([$inventory_id]);

            $remaining_quantity_to_release -= $inventory_quantity;

            // Record release history with positive quantity
            $stmt = $pdo->prepare("INSERT INTO release_history (product_name, quantity, date_released, storage_type)
                                   VALUES (?, ?, NOW(), ?)");
            $stmt->execute([$product_name, $inventory_quantity, $item['storage_type']]);
        } else {
            // Partially release stock
            $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$remaining_quantity_to_release, $inventory_id]);

            // Record release history with positive quantity
            $stmt = $pdo->prepare("INSERT INTO release_history (product_name, quantity, date_released, storage_type)
                                   VALUES (?, ?, NOW(), ?)");
            $stmt->execute([$product_name, $remaining_quantity_to_release, $item['storage_type']]);

            $remaining_quantity_to_release = 0;
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// Remove products with zero quantity from the inventory table
$pdo->exec("DELETE FROM inventory WHERE quantity = 0");

// Fetch all products for inventory visualization
$inventory = [];
$stmt = $pdo->query("SELECT product_name, quantity, date_received, storage_type, expiration_date FROM inventory WHERE quantity > 0 ORDER BY date_received ASC");
$inventory = $stmt->fetchAll();

// Fetch release history
$release_history = [];
$stmt_history = $pdo->query("SELECT product_name, quantity, date_released, storage_type FROM release_history ORDER BY date_released DESC");
$release_history = $stmt_history->fetchAll();
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
            background: url('2.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 20px;
            
        }
        h1 {
            text-align: center;
            color: white;
            background-color: #3cb371;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        h2 {
            text-align: center;
            color: #3cb371; /* Green color for headings */
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        .button {
            padding: 10px 20px;
            background-color: #3cb371;
            color: white;
            border: none;
            cursor: pointer;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #006400;
        }
        table {
            width: 90%; /* Adjusted width */
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        th {
            background-color: #3cb371; /* Green header background */
            color: white;
        }
        td {
            background-color: #fefefa;
            
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            padding: 10px;
            width: 60%;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 0 auto;
        }
        .flex-container {
            display: flex;
            justify-content: space-around; /* Align tables side by side with space around */
            margin-top: 40px;
        }
        .table-container {
            width: 45%; /* Set width for each table container */
        }
        .table-container table {
            width: 100%; /* Ensure each table takes full width of its container */
        }
        .login-button {
            padding: 10px 20px;
            background-color: #483d8b; /* Same green as other buttons */
            color: white;
            border: none;
            cursor: pointer;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: absolute; /* Position it absolutely */
            top: 1px; /* Distance from the bottom */
            right: 20px; /* Distance from the right */
            text-align: right; /
        }

        .login-button:hover {
            background-color: #c40233 ; /* Darker green on hover */
        }

        
            
            
        
    </style>
</head>
<body>

    <h1>Inventory Management System Of Starbucks</h1>

    <div class="button-container" style="text-align: center;">
        <button onclick="document.getElementById('addForm').style.display='block'" class="button">Add Product</button>
        <button onclick="document.getElementById('releaseForm').style.display='block'" class="button">Release Product</button>
        
        <!-- Login Button -->
        <?php if (!isset($_SESSION['user_id'])): // Show only if not logged in ?>
            <a href="lan.php" class="login-button">Logout</a>
        <?php else: ?>
            <p>Welcome, User! <a href="logout.php">Logout</a></p> <!-- Optional logout link -->
        <?php endif; ?>
    </div>

    

    <!-- Add Product Form -->
    <div id="addForm" style="display:none; text-align:center;">
        <h2>Add Product</h2>
        <form method="POST" action="">
            <label>Product Name:</label><br>
            <input type="text" name="product_name" required><br>
            <label>Quantity:</label><br>
            <input type="number" name="quantity" required><br>
            <label>Storage Type:</label><br>
            <select name="storage_type" required>
                <option value="Freezer">Freezer</option>
                <option value="Fridge">Fridge</option>
                <option value="Room Temperature">Room Temperature</option>
            </select><br>
            <label>Expiration Date:</label><br>
            <input type="date" name="expiration_date" required><br> <!-- New expiration date input -->
            <input type="submit" name="add_product" value="Add Product" class="button">
        </form>
    </div>

    <!-- Release Product Form -->
    <div id="releaseForm" style="display:none; text-align:center;">
        <h2>Release Product</h2>
        <form method="POST" action="">
            <label>Product Name:</label><br>
            <input type="text" name="product_name" required><br>
            <label>Quantity to Release:</label><br>
            <input type="number" name="quantity" required><br>
            <input type="submit" name="release_product" value="Release Product" class="button">
        </form>
    </div>

    

    <!-- Flex container for side-by-side layout -->
    <div class="flex-container">
        <!-- Current Inventory Table -->
        <div class="table-container">
            <h2>Current Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Date Received</th>
                        <th>Storage Type</th>
                        <th>Expiration Date</th> <!-- Single expiration date column -->
                    </tr>
                </thead>
                <tbody>
    <?php
    if ($inventory):
        // Create an associative array to sum the quantities by product name
        $totalQuantities = [];

        // First loop to sum the quantities by product name
        foreach ($inventory as $item) {
            $productName = $item['product_name'];
            if (!isset($totalQuantities[$productName])) {
                $totalQuantities[$productName] = 0;
            }
            $totalQuantities[$productName] += $item['quantity'];
        }

        // Second loop to display the inventory items and warnings
        foreach ($inventory as $item):
            $productName = $item['product_name'];
            $quantity = $item['quantity'];
            $lowStockThreshold = 10; // Change this value as needed for low stock alert
            $totalQuantity = $totalQuantities[$productName]; // Get the total quantity for the product
    ?>
            <tr>
                <td><?php echo htmlspecialchars($productName); ?></td>
                <td>
                    <?php
                        // Display the quantity of this individual entry
                        echo $quantity;

                        // Show LowStock warning only if the total quantity for the product is below the threshold
                        if ($totalQuantity < $lowStockThreshold) {
                            echo ' <span style="color: red; font-weight: bold; background-color: #ffcccb; padding: 2px 5px; border-radius: 5px;">(LowStock!)</span>';
                        }
                    ?>
                </td>
                <td><?php echo $item['date_received']; ?></td>
                <td><?php echo ucfirst($item['storage_type']); ?></td>
                <td>
                    <?php
                        $currentDate = date('Y-m-d');
                        $expirationDate = $item['expiration_date'];
                        $oneWeekAhead = date('Y-m-d', strtotime('+1 week', strtotime($currentDate)));

                        // Check if the product is expired
                        if ($expirationDate < $currentDate) {
                            echo '<span style="color:red;">' . htmlspecialchars($expirationDate) . ' (Expired)</span>';
                        } elseif ($expirationDate <= $oneWeekAhead) {
                            // Display an alert for products expiring within 7 days
                            echo '<span style="color:orange;">' . htmlspecialchars($expirationDate) . ' (ExpiringSoon)</span>';
                        } else {
                            echo htmlspecialchars($expirationDate);
                        }
                    ?>
                </td>
            </tr>
    <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="5">No products in inventory.</td>
        </tr>
    <?php endif; ?>
</tbody>

                
   
            </table>
        </div>

        <!-- Release History Table -->
        <div class="table-container">
            <h2>Release History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity Released</th>
                        <th>Date Released</th>
                        <th>Storage Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($release_history): ?>
                        <?php foreach ($release_history as $history): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($history['product_name']); ?></td>
                                <td><?php echo $history['quantity']; ?></td>
                                <td><?php echo $history['date_released']; ?></td>
                                <td><?php echo ucfirst($history['storage_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No release history available.</td> <!-- Adjust colspan to 4 -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>