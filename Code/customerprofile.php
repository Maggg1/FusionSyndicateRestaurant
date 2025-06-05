<?php
session_start();
include 'classes/connectDb.php';

// Redirect if user is not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$DB = new DataBase();
$conn = $DB->connect();

// Fetch user details securely
$query = "SELECT * FROM users WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Fetch purchase history
$query_purchases = "SELECT * FROM purchases WHERE user_id = ? ORDER BY id DESC";
$stmt_purchases = $conn->prepare($query_purchases);
$stmt_purchases->bind_param("i", $user_id);
$stmt_purchases->execute();
$result_purchases = $stmt_purchases->get_result();
$purchases = $result_purchases->fetch_all(MYSQLI_ASSOC);

$stmt_purchases->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       body {
    background: #f2f2e7;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
}

nav {
    background-color: #4CAF50;
    padding: 10px;
    text-align: center;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
}

nav li {
    margin: 0 15px;
}

nav a {
    text-decoration: none;
    color: white;
    padding: 10px;
    transition: 0.3s;
}

nav a:hover {
    background-color: #04AA6D;
    border-radius: 5px;
}

.button {
    display: inline-block;
    padding: 6px 12px;  /* Smaller size */
    font-size: 14px;   /* Reduce font size */
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

button:hover, .btn:hover {
    background: #028a57;
}

.profile-info, .edit-profile, .purchase-history {
    background: white;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: left;
}

.profile-info p {
    margin: 8px 0;
}

#gmbr {
    width: 100px;
    border-radius: 50%;
    margin: 15px auto;
    display: block;
}

.edit-profile {
    display: none;
}

.edit-profile input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.purchase-history {
    max-width: 600px;
}

.purchase-history table {
    width: 100%;
    border-collapse: collapse;
}

.purchase-history th, .purchase-history td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.purchase-history th {
    background: #04AA6D;
    color: white;
}



    </style>
</head>
<body>

    <section class="first">
        <div style="font-size:30px; text-align:center; padding:20px;">
            Welcome, <?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?>
            <br>This is Your Profile.
        </div>
        <div class="button">
                <a href="index.php" class="button">Home</a>
                <a href="logout.php" class="button" onclick="return confirmLogout()">Logout</a>

		<script>
		function confirmLogout() {
			return confirm("Are you sure you want to logout?");
		}
		</script>
            </div>
    </section>

    <section style="text-align:center; padding:20px;">
        <nav>
            <ul>
                <li><a href="menu_cart.php">Menus</a></li>
                <li><a href="AboutUs.php">About Us</a></li>
                <li><a href="trackorder.php">Track Order</a></li>
            </ul>
        </nav>
    </section>

    <section style="text-align:center;">
        <img id="gmbr" src="img/profile.png" alt="Profile Picture">
        <div class="profile-info">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>

        <!-- Button to show the edit form -->
        <button class="button" onclick="toggleEditForm()">Change Details</button>

        <!-- Edit Profile Form (hidden by default) -->
        <div class="edit-profile" id="editProfileForm">
            <h3>Edit Profile</h3>
            <form action="update_profile.php" method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>">

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_number']) ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

                <button type="submit" onclick="confirmUpdate(event)">Save Changes</button>

            </form>
        </div>

        <!-- PURCHASE HISTORY -->
<div class="purchase-history">
    <h3>Purchase History</h3>
    <?php if (empty($purchases)): ?>
        <p>No purchase history available.</p>
    <?php else: ?>
        <table border="1" cellspacing="0" cellpadding="8">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Order ID</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grouped_orders = [];
                foreach ($purchases as $purchase) {
                    $date = date('Y-m-d', strtotime($purchase['purchase_date']));
                    $grouped_orders[$date][] = $purchase;
                }

                foreach ($grouped_orders as $date => $orders):
                ?>
                    <tr>
                        <td rowspan="<?= count($orders) ?>" style="vertical-align: middle; text-align: center; font-weight: bold;">
                            <?= htmlspecialchars($date) ?>
                        </td>
                        <?php foreach ($orders as $index => $purchase): ?>
                            <?php if ($index > 0): ?><tr><?php endif; ?>
                                <td>#<?= htmlspecialchars($purchase['id']) ?></td>
                                <td><?= htmlspecialchars($purchase['item_name']) ?></td>
                                <td>RM<?= htmlspecialchars($purchase['price']) ?></td>
                                <td><?= htmlspecialchars($purchase['quantity']) ?></td>
                                <td>Rm<?= htmlspecialchars($purchase['price'] * $purchase['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

    </section>

    <script>
    function toggleEditForm() {
        const editForm = document.getElementById('editProfileForm');
        editForm.style.display = editForm.style.display === 'none' || editForm.style.display === '' ? 'block' : 'none';
    }

    function confirmUpdate(event) {
        if (!confirm("Are you sure you want to update your details?")) {
            event.preventDefault(); // Stop form submission if user cancels
        }
    }
</script>


</body>
</html>
