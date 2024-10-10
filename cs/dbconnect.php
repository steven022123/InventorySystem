<?php
// Database connection settings
$host = 'localhost';
$db = 'crilyn';  // Your database name
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; // Define the charset variable

// Set up the DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Create a new PDO instance with the given DSN and credentials
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // Set the PDO error mode to warning

// Add product to a specific storage location
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $location = $_POST['storage_location'];  // Get the storage location
    $date_received = date('Y-m-d H:i:s');

    // Whitelist locations to prevent SQL injection
    $allowed_locations = ['freezer', 'fridge', 'room_temp'];
    
    if (in_array($location, $allowed_locations)) {
        // Insert product into the respective inventory based on storage location
        $stmt = $pdo->prepare("INSERT INTO $location (product_name, quantity, date_received) VALUES (?, ?, ?)");
        $stmt->execute([$name, $quantity, $date_received]);

        echo "<div class='message success'>Product added successfully to $location!</div>";
    } else {
        echo "<div class='message error'>Invalid storage location selected!</div>";
    }
}

// Release product based on FIFO from a specific storage location
if (isset($_POST['release_product'])) {
    $name = $_POST['product_name'];
    $quantity_to_release = $_POST['quantity'];
    $location = $_POST['storage_location'];  // Get the storage location

    // Whitelist locations to prevent SQL injection
    $allowed_locations = ['freezer', 'fridge', 'room_temp'];

    if (in_array($location, $allowed_locations)) {
        // Fetch products by FIFO (oldest first) from the respective location
        $stmt = $pdo->prepare("SELECT * FROM $location WHERE product_name = ? AND quantity > 0 ORDER BY date_received ASC");
        $stmt->execute([$name]);
        $rows = $stmt->fetchAll();

        $total_quantity = 0;
        foreach ($rows as $row) {
            $total_quantity += $row['quantity'];
        }

        // Check if there is enough stock to release
        if ($total_quantity < $quantity_to_release) {
            echo "<div class='message error'>Not enough stock in $location to release!</div>";
        } else {
            foreach ($rows as $row) {
                $release_quantity = min($quantity_to_release, $row['quantity']);
                $quantity_to_release -= $release_quantity;

                // Deduct from the inventory (FIFO order)
                $stmt_update = $pdo->prepare("UPDATE $location SET quantity = quantity - ? WHERE id = ?");
                $stmt_update->execute([$release_quantity, $row['id']]);

                // Insert into release history
                $stmt_history = $pdo->prepare("INSERT INTO release_history (product_name, quantity, date_released, storage_location) VALUES (?, ?, ?, ?)");
                $stmt_history->execute([$name, $release_quantity, date('Y-m-d H:i:s'), $location]);

                if ($quantity_to_release == 0) {
                    break;
                }
            }
            echo "<div class='message success'>Stock released successfully from $location!</div>";
        }
    } else {
        echo "<div class='message error'>Invalid storage location selected!</div>";
    }
}





// Fetch release history
$stmt_history = $pdo->query("SELECT * FROM release_history ORDER BY date_released DESC");
$release_history = $stmt_history->fetchAll();

?>