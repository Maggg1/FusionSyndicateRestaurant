<?php
session_start();
include("classes/connectDb.php"); // Ensure the database connection file is included

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Validate item_id and quantity from POST request
$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($item_id === false || $quantity === false || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item or quantity']);
    exit();
}

// Connect to the database
$db = new DataBase();
$conn = $db->connect();

// Fetch item details from `menu_items`
$stmt = $conn->prepare("SELECT name, price, image FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit();
}

$item = $result->fetch_assoc();
$stmt->free_result();
$stmt->close();

// Ensure cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update the item in the cart
if (isset($_SESSION['cart'][$item_id])) {
    $_SESSION['cart'][$item_id]['quantity'] += $quantity; // Increase quantity if exists
} else {
    $_SESSION['cart'][$item_id] = [
        'name' => $item['name'],
        'price' => $item['price'],
        'image' => $item['image'],
        'quantity' => $quantity
    ];
}

// Calculate total cart count
$totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));

// Return success response with updated cart count
echo json_encode(['success' => true, 'cart_count' => $totalItems]);
?>
