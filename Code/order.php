<?php 
include 'classes/connect.php';  // Include the DataBase class

// Create an instance of the DataBase class
$db = new DataBase();

// Connect to the database
$conn = $db->connect();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to check and create the Orders table if it doesn't exist
function ensureOrdersTableExists($db) {
    $sql = "CREATE TABLE IF NOT EXISTS Orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total DECIMAL(10, 2) NOT NULL,
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'Pending',
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    );";

    if (!$db->save($sql)) {
        die("Error creating Orders table.");
    }
}

// Ensure the Orders table is ready before any operations are performed
ensureOrdersTableExists($db);

// Check if the user exists
$user_id = 1; // Example user_id; ensure you retrieve this from session or other means

// Prepare a statement to check for the user
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($userExists);
$stmt->fetch();
$stmt->close();

if ($userExists == 0) {
    die("User ID does not exist. Please register or log in.");
}

// Proceed with your order processing logic
$total = 100.00; // Example total; calculate this from cart data

$stmt = $conn->prepare("INSERT INTO Orders (user_id, total, status) VALUES (?, ?, 'Pending')");
$stmt->bind_param("id", $user_id, $total);
if ($stmt->execute()) {
    echo "Order placed successfully! Order ID: " . $stmt->insert_id;
} else {
    echo "Error placing order: " . $stmt->error;
}

// Clean up
$stmt->close();
$conn->close();
?>


