<?php
session_start();
include 'classes/connectDb.php';

$db = new DataBase();
$conn = $db->connect();

$loggedIn = isset($_SESSION['id']);
$user_id = $_SESSION['id'] ?? null;
$user_role = $_SESSION['role'] ?? '';
$username = $_SESSION['first_name'] ?? '';
$user_address = isset($_SESSION['address']) && !empty($_SESSION['address']) 
    ? $_SESSION['address'] 
    : 'Kuala Lumpur, Malaysia';
	
	if (!isset($_SESSION['address']) || empty($_SESSION['address'])) {
    $sql = "SELECT address FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $user_address = $row['address'];
            $_SESSION['address'] = $user_address; // Store in session for future use
        }
    }
}


$invoice_number = null;
$purchase_status = "No recent purchases found.";
$purchased_items = [];
$payment_method = "";
$purchase_date = "";

if ($loggedIn && $user_id) {
    // Step 1: Get the latest invoice number for the user
    $sql = "SELECT invoice_number, payment_method, purchase_date 
            FROM purchases WHERE user_id = ? 
            ORDER BY purchase_date DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $invoice_number = $row['invoice_number'];
            $payment_method = $row['payment_method'];
            $purchase_date = $row['purchase_date'];
            $purchase_status = "On Delivery"; // Change if needed

            // Step 2: Fetch ALL items related to this invoice number
            $sqlItems = "SELECT item_name, price, quantity 
                         FROM purchases WHERE invoice_number = ?";
            $stmtItems = $conn->prepare($sqlItems);
            $stmtItems->bind_param("s", $invoice_number);
            $stmtItems->execute();
            $purchased_items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Purchase</title>
	<link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            text-align: center;
        }
        .track-container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .order-info {
            margin-bottom: 20px;
        }
        .order-status {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #ff9800;
        }
        .purchased-items {
            text-align: left;
            margin-top: 10px;
        }
        .purchased-items ul {
            list-style-type: none;
            padding: 0;
        }
        .purchased-items li {
            background: #f4f4f4;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .progress-container {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
        }
        .progress-bar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            border-radius: 10px;
            text-align: center;
            line-height: 20px;
            color: white;
            font-weight: bold;
            transition: width 1s ease-in-out;
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
    </div>
    <div class="track-container">
        <h2>Track Your Purchase</h2>

        <?php if ($user_id): ?>
            <div class="order-info">
                <p><strong>Invoice Number:</strong> #<?= htmlspecialchars($invoice_number, ENT_QUOTES, 'UTF-8') ?></p>
				<p><strong>Delivery Address:</strong> <?= htmlspecialchars($user_address, ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8') ?></p>
				<p><strong>Purchase Date:</strong> <?= htmlspecialchars($purchase_date, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
			
			<div class="order-status" id="orderStatus">🚚 Status: Cooking</div>


          <div class="purchased-items">
    <h3>Purchased Items:</h3>
    <?php if (!empty($purchased_items)): ?>
        <ul>
            <?php foreach ($purchased_items as $item): ?>
                <li><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?> 
                    (x<?= (int) $item['quantity'] ?>) - RM<?= number_format((float) $item['price'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No items purchased.</p>
    <?php endif; ?>
</div>

            <!-- Progress Bar -->
            <div class="progress-container">
			<div class="progress-bar" id="progressBar">Cooking (0%)</div>
			</div>

        <?php else: ?>
            <p>No recent purchases found.</p>
        <?php endif; ?>
    </div>

    <script>
    let progress = 0;
    let statuses = ["Cooking", "Preparing for Delivery", "Sent to Customer"];
    let index = 0;

    function updateProgress() {
        if (progress < 100) {
            progress = Math.min(progress + 33, 100); // Ensure it doesn't go beyond 100%
            index = Math.min(index + 1, statuses.length - 1);

            document.getElementById("progressBar").style.width = progress + "%";
            document.getElementById("progressBar").innerText = " (" + progress + "%)";

            // Update order status text
            document.getElementById("orderStatus").innerHTML = "🚚 Status: " + statuses[index];

            setTimeout(updateProgress, 4000); // Delay between each step
        } else {
            // When progress reaches 100%, set status to "Done Delivery"
            document.getElementById("progressBar").innerText = "Done Delivery (100%)";
            document.getElementById("orderStatus").innerHTML = "🚚 Status: Done Delivery (100%)";

            <?php if (strtolower($payment_method) === 'cash on delivery'): ?>
            // Show message only if payment method is COD
            const codMessage = document.createElement("p");
            codMessage.innerHTML = "<strong style='color:red;'>Please make full payment before taking the food.</strong>";
            codMessage.style.marginTop = "20px";
            document.querySelector(".track-container").appendChild(codMessage);
            <?php endif; ?>
        }
    }

    updateProgress();
</script>




	
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
</body>
</html>
