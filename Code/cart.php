<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include("classes/connectDb.php");

$db = new DataBase();
$conn = $db->connect();

// Fetch cart items from session
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;

// Fetch menu items securely
if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $sql = "SELECT id, name, price, image FROM menu_items WHERE id IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($cart)), ...array_keys($cart));
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $item_id = $row['id'];
        $quantity = intval($cart[$item_id]['quantity']);
        $price = floatval($row['price']);

        $cart_items[] = [
            'id' => $item_id,
            'name' => $row['name'],
            'price' => $price,
            'quantity' => $quantity,
            'image' => $row['image']
        ];
        
        $total_price += $price * $quantity;
    }
    
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .cart-container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .cart-container h2 {
            text-align: center;
        }
        .cart-items {
            margin-top: 20px;
        }
        .cart-items ul {
            list-style-type: none;
            padding: 0;
        }
        .cart-items li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .purchase-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }
        .purchase-button:hover {
            background-color: #218838;
        }
        .cart-item-controls {
            display: flex;
            align-items: center;
        }
        .control-button {
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
    <script>
        function updateQuantity(itemId, action) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}&action=${action}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to update cart.');
                }
            });
        }

        function removeItem(itemId) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}&action=remove`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to remove item.');
                }
            });
        }
    </script>
</head>
<body>
    <div class="cart-container">
        <h2>Your Cart</h2>
        <div class="cart-items">
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <span><?= htmlspecialchars($item['name']) ?> - Rm <?= number_format($item['price'], 2) ?> x <?= htmlspecialchars($item['quantity']) ?></span>
                            <div class="cart-item-controls">
                                <button class="control-button" onclick="updateQuantity(<?= $item['id'] ?>, 'increase')">+</button>
                                <button class="control-button" onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')">-</button>
                                <button class="control-button" onclick="removeItem(<?= $item['id'] ?>)">Remove</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="cart-total">
                    <strong>Total Price: Rm <?= number_format($total_price, 2) ?></strong>
                </div>
            <?php endif; ?>
        </div>
		
        <?php if (!empty($cart_items)): ?>
            <form action="payment_gateway.php" method="POST">
                <button type="submit" class="purchase-button">Proceed to Payment</button>
				
			</form>
        <?php endif; ?>
		<div style="margin-top: 20px;">
			<form action="menu_cart.php" method="GET">
			<button type="submit" class="purchase-button" style="background-color: #007bff;">Back to Menu</button>
			</form>
		</div>

    </div>
</body>
</html>
