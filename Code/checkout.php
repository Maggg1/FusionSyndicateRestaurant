<?php 
session_start();
include("header.php");// Include the header for consistency
include_once("classes/connect.php");// Include the DataBase class
include("classes/login.php");
include_once("classes/users.php");


echo '<link rel="stylesheet" href="styles.css">';  // Link to your CSS file

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    echo "Your cart is empty. Please go back and add items to your cart.";
    exit;
}

// Create an instance of the DataBase class
$db = new DataBase();

// Connect to the database
$conn = $db->connect();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Checkout Complete</h1>";
echo "<p>Thank you for your order. Here are the details of your purchase:</p>";

$total = 0;
echo "<ul>";
foreach ($_SESSION['cart'] as $item_id => $quantity) {
    $stmt = $db->read("SELECT name, price FROM menu_items WHERE id = " . intval($item_id));
    if ($stmt) {
        $name = htmlspecialchars($stmt[0]['name']);
        $price = $stmt[0]['price'];
        $item_total = $price * $quantity;
        $total += $item_total;
        echo "<li>$name - Quantity: $quantity at \$$price each</li>";
    }
}
echo "</ul>";
echo "<p>Total Cost: \$" . number_format($total, 2) . "</p>";

// Optionally create a record of the transaction in the database
$user_id = $_SESSION['userid'] ?? null;  // Change this line to match the session variable name
if ($user_id) {
    $insert_query = "INSERT INTO orders (userid, total, order_date, status) VALUES (?, ?, NOW(), 'Completed')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("id", $user_id, $total);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        echo "<p>Order ID: $order_id</p>";
    } else {
        echo "Error placing order: " . $stmt->error;
    }
} else {
    echo "You must be logged in to place an order.";
}

// Clear the session cart after checkout
unset($_SESSION['cart']);

// Close the connection
$conn->close();

// Optionally redirect or allow further actions
echo '<a href="index.php">Return to Home</a>';
?>
