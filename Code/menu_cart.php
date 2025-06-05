<?php
session_start();  // Start the session at the very beginning

// Include the DataBase class
include("classes/connectDb.php");

// Create a new instance of the DataBase class
$db = new DataBase();

// Create the database if it doesn't exist
$db->save("CREATE DATABASE IF NOT EXISTS profile_db");

// Select the database
$conn = $db->connect();
mysqli_select_db($conn, "profile_db");

// Create the `menu_items` table if it doesn't exist
$table_sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
)";
$db->save($table_sql);

// Insert sample data only if the table is empty
$check_sql = "SELECT COUNT(*) AS cnt FROM menu_items";
$check_result = $db->read($check_sql);

if ($check_result && count($check_result) > 0 && $check_result[0]['cnt'] == 0) {
    $insert_sql = "INSERT INTO menu_items (name, price, category, image) VALUES
    ('Roasted Chicken', 10.99, 'Western Cuisine', 'western2.jpg'),
    ('Spaghetti Bolognese', 21.99, 'Western Cuisine', 'western1.jpg'),
    ('Bakso Special', 12.99, 'Asian Cuisine', 'asian2.jpg'),
    ('Nasi Lemak', 10.99, 'Asian Cuisine', 'asian1.jpg'),
    ('Hummus Special', 12.99, 'Middle Eastern Cuisine', 'me1.jpg'),
    ('Shawarma', 15.99, 'Middle Eastern Cuisine', 'me2.jpg'),
    ('Cajun Shrimp Po Boy', 20.99, 'Limited Order', 'special1.jpg')";
    $db->save($insert_sql);
}

// Fetch distinct categories dynamically, excluding "Fresh Drink"
$category_query = "SELECT DISTINCT category FROM menu_items WHERE category != 'Fresh Drink'";
$category_result = $conn->query($category_query);

$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Check if user is logged in
$loggedIn = isset($_SESSION['id']); 
$user_role = $_SESSION['role'] ?? '';
$username = $_SESSION['first_name'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Menu</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="menu.css">
    
    <script>
        function addToCart(itemId) {
    const quantity = document.querySelector(`input[name="quantity_${itemId}"]`).value;

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}&quantity=${quantity}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            const notification = document.getElementById('cart-notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000);
        } else {
            alert(data.message);
        }
    });
}

function updateCartCount(count) {
    document.getElementById('cart-count').textContent = count;
}

// Ensure the cart count is correct when the page loads
document.addEventListener("DOMContentLoaded", function() {
    fetch('cart_count.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('cart-count').textContent = data.count;
    });
});

    </script>
</head>
<body>
    <header>
        <div class="head1">
            <div class="logo">
                <a href="index.html">
                    <img src="img/logo.png" alt="Fusion Syndicate Logo" />
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu_cart.php">Menus</a></li>
                    <li><a href="AboutUs.php">About Us</a></li>
                </ul>
            </nav>
            <div class="icons">
                <?php if ($loggedIn): ?>
                    <a href="<?= ($user_role === 'admin') ? 'adminprofile.php' : 'customerprofile.php' ?>" class="profile">
                        <img src="img/profile.png" alt="Profile" class="icon">
                        <span>Welcome, <?= htmlspecialchars(ucfirst($username), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(ucfirst($user_role), ENT_QUOTES, 'UTF-8') ?>)</span>
                    </a>

                    <?php if ($user_role !== 'admin'): ?>
                        <a href="cart.php">
                            <img src="img/Cart.png" alt="Cart" class="icon">
							<span id="cart-count" class="cart-badge">0</span>
                            <span class="login-text">Browse Cart</span>
                        </a>
                    <?php endif; ?>

                    <a href="logout.php" onclick="return confirmLogout();">
					<img src="img/logout.png" alt="Logout" class="icon">
					<span class="login-text">Logout</span>
					</a>
					<!--Logout Verification-->
					<script>
					function confirmLogout() {
					return confirm("Are you sure you want to log out?");
					}
					</script>
					
                <?php else: ?>
                    <a href="login.php" class="login">
                        <img src="img/profile.png" alt="Profile" class="icon">
                        <span class="login-text">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <h2>Menu</h2>
    <?php
    foreach ($categories as $category) {
        echo "<h3 class='menu-section-header'>$category</h3>";
        
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<ul class='menu-list'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><img src='img/menu/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='menu-item-image'>";
                echo "<span class='menu-item-name'>" . htmlspecialchars($row['name']) . "</span>";
                echo "<span class='menu-item-price'>RM " . htmlspecialchars($row['price']) . "</span>";
                echo "<form onsubmit='event.preventDefault(); addToCart(" . $row['id'] . ");'>";
                echo "<label>Quantity: </label>";
                echo "<input type='number' name='quantity_" . $row['id'] . "' min='1' value='1'>";
                echo "<button type='submit' class='add-to-cart-btn'>Add to Cart</button>";
                echo "</form></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No items available in this category.</p>";
        }
    }
    $conn->close();
    ?>
</body>
</html>
