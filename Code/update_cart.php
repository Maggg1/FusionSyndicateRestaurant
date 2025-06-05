<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['item_id'], $_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$item_id = intval($_POST['item_id']);
$action = $_POST['action'];

// Ensure the cart exists
if (!isset($_SESSION['cart'][$item_id])) {
    echo json_encode(['success' => false, 'message' => 'Item not in cart']);
    exit();
}

switch ($action) {
    case 'increase':
        $_SESSION['cart'][$item_id]['quantity']++;
        break;
    case 'decrease':
        if ($_SESSION['cart'][$item_id]['quantity'] > 1) {
            $_SESSION['cart'][$item_id]['quantity']--;
        } else {
            unset($_SESSION['cart'][$item_id]); // Remove if quantity is 0
        }
        break;
    case 'remove':
        unset($_SESSION['cart'][$item_id]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
}

// Get updated cart count
$totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));

echo json_encode(['success' => true, 'cart_count' => $totalItems]);
?>
