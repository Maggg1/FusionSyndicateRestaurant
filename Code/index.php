<?php
session_start();

// Debugging: Print session data
/*echo '<pre>';
print_r($_SESSION);
echo '</pre>';*/

// Database Connect
include 'classes/connectDb.php';
require_once 'classes/connectDb.php';
$db = new DataBase();
$conn = $db->connect();

// Check if user is logged in
$loggedIn = isset($_SESSION['id']); // Use 'id' or another unique session variable
$user_role = $_SESSION['role'] ?? '';
$username = $_SESSION['first_name'] ?? ''; // Use 'first_name' for the welcome message

// Define categories to display
$categories = ["Limited Order", "Dessert"]; // Change categories as needed

$menuItems = [];

foreach ($categories as $category) {
    // Fetch 2 items per category (you can adjust this limit)
    $query = "SELECT * FROM menu_items WHERE category IN ('Limited Order', 'Dessert') LIMIT 5";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $menuItems = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Query preparation failed: " . $conn->error);
}

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fusion Syndicate Online Ordering System</title>
    <link rel="stylesheet" href="styles.css">
	<style>
        .menu-section {
            text-align: center;
            padding: 50px 20px;
            background: #f8f8f8;
        }
        .menu-title {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }
        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: auto;
        }
        .menu-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        .menu-item img {
			width: 100%;  /* Make image fill the container */
			height: auto; /* Maintain aspect ratio */
			max-height: 200px; /* Optional: Limit height */
			object-fit: contain; /* Ensure the whole image fits without cropping */
			display: block;
			margin: 0 auto; /* Center the image */
		}
        .menu-item h3 {
            font-size: 1.2em;
            margin: 10px 0;
            color: #444;
        }
        .menu-item .price {
            font-size: 1.1em;
            font-weight: bold;
            color: #d9534f;
        }
        .menu-item a {
            display: block;
            margin-top: 10px;
            background: #d9534f;
            color: white;
            text-decoration: none;
            padding: 8px;
            border-radius: 5px;
        }
        .menu-item a:hover {
            background: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="head1">
                <div class="logo">
                    <a href="index.php">
                        <img src="img/logo.png" alt="Fusion Syndicate Logo">
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
					<!--only change depends on user login-->
					<?php if ($loggedIn): ?>
					<a href="<?= ($user_role === 'admin') ? 'adminprofile.php' : 'customerprofile.php' ?>" class="profile">
					<img src="img/profile.png" alt="Profile" class="icon">
					<span>Welcome, <?= htmlspecialchars(ucfirst($username), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(ucfirst($user_role), ENT_QUOTES, 'UTF-8') ?>)</span>
					</a>

					<?php if ($user_role !== 'admin'): ?> <!-- Hide Cart for admin -->
					<a href="cart.php">
					<img src="img/Cart.png" alt="Cart" class="icon">
					<span class="login-text">Cart</span>
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

        <section class="first">
            <div class="yessir">
                <h1>The Best Restaurant in Kota Kinabalu</h1>
                <p>100% satisfaction guaranteed! If you are not happy, we will make it right.</p>
                <a href="menu_cart.php" class="button">Order Now</a>
            </div>
        </section>
		
		<!-- Limited Menu Section -->
        <section class="menu-section">
            <h2 class="menu-title">Featured Menu</h2>
            <div class="menu-container">
                <?php foreach ($menuItems as $item): ?>
                    <div class="menu-item">
                        <img src="img/menu/<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <span class="price">RM<?= number_format($item['price'], 2) ?></span>
                        <a href="menu_cart.php">Order Now</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>


        <footer>
            <div class="footer-social-links">
                <a href="https://www.facebook.com/" target="_blank">
                    <img src="img/fb.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/" target="_blank">
                    <img src="img/ig.jpg" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/" target="_blank">
                    <img src="img/tt.png" alt="TikTok">
                </a>
            </div>
            <div class="footer-copy">
                <p>&copy; 2024 Fusion Syndicate Online Ordering System</p>
            </div>
        </footer>
    </div>
</body>
</html>