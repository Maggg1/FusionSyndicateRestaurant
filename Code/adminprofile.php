<?php
session_start();
if (!isset($_SESSION['email']) || strpos($_SESSION['email'], '.admin') === false) {
    header("Location: login.php");
    exit();
}

include("classes/connectDb.php");
$db = new DataBase();
$conn = $db->connect();
mysqli_select_db($conn, "profile_db");

// Fetch date filter values
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Fetch feedback email filter
$feedback_email = isset($_GET['feedback_email']) ? $_GET['feedback_email'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="picture.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        h2, h3 {
            text-align: center;
            color: #2c3e50;
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
            padding: 6px 12px;
            font-size: 14px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .button:hover {
            background-color: #367c39;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        img#gmbr {
            width: 100px;
            border-radius: 50%;
            display: block;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<section class="first">
    <div style="font-size:30px;">
        <div style="padding-top:10px;">
            <?php
            echo "<p>Welcome Admin, " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "!</p>";
            echo "<p>Email: " . $_SESSION['email'] . "</p>";
            ?>
        </div>
        <div class="button">
            <a href="index.php" class="button">Home</a>
            <a href="logout.php" class="button" onclick="return confirmLogout()">Logout</a>
        </div>
    </div>
</section>

<nav>
    <ul>
        <li><a href="admin_menu.php">Manage Menu</a></li>
        <li><a href="menu_cart.php">Menus</a></li>
        <li><a href="AboutUs.html">About Us</a></li>
    </ul>
</nav>

<section>
    <div>
        <img id="gmbr" src="img/profile.png">
    </div>
</section>

<div class="customer-list">
    <h3>Customer Purchase History</h3>

    <!-- Filter Form -->
<form method="GET">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
    
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
    
    <button type="submit">Filter</button>
    <button type="button" onclick="clearFilters()">Clear</button>
</form>

<script>
function clearFilters() {
    window.location.href = "adminprofile.php"; // Reloads the page without filters
}
</script>


<table>
    <tr>
        <th>Customer Name</th>
        <th>Item Purchased</th>
        <th>Quantity</th>
        <th>Price (RM)</th>
        <th>Total Amount (RM)</th>
        <th>Payment Method</th>
        <th>Invoice Number</th>
        <th>Purchase Date</th>
    </tr>
    <?php
    include("classes/connectDb.php");
    $db = new DataBase();
    $conn = $db->connect();
    mysqli_select_db($conn, "profile_db");

    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

    $query = "SELECT CONCAT(users.first_name, ' ', users.last_name) AS name, 
                     purchases.item_name, purchases.quantity, 
                     purchases.price, (purchases.quantity * purchases.price) AS total_amount, 
                     purchases.payment_method, purchases.invoice_number, purchases.purchase_date 
              FROM purchases 
              JOIN users ON purchases.user_id = users.id";

    if ($start_date && $end_date) {
        $query .= " WHERE purchases.purchase_date BETWEEN '$start_date' AND '$end_date'";
    }

    $query .= " ORDER BY users.id, purchases.purchase_date";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['item_name']}</td>
                <td>{$row['quantity']}</td>
                <td>Rm{$row['price']}</td>
                <td>Rm{$row['total_amount']}</td>
                <td>{$row['payment_method']}</td>
                <td>{$row['invoice_number']}</td>
                <td>{$row['purchase_date']}</td>
              </tr>";
    }
    ?>
</table>


</div>

<div class="comments-section">
    <h3>Customer Feedback</h3>

    <!-- Filter Form -->
    <form method="GET">
        <label>Email: <input type="email" name="feedback_email" value="<?= $feedback_email ?>"></label>
        <button type="submit">Filter</button>
    </form>

    <table>
    <tr>
        <th>Customer Name</th>
        <th>Customer Email</th>
        <th>Comment</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php
    $feedbackQuery = "SELECT id, name, email, comment, created_at FROM feedback";
    if ($feedback_email) {
        $feedbackQuery .= " WHERE email = '$feedback_email'";
    }
    $feedbackQuery .= " ORDER BY created_at DESC";
    $feedbackResult = $conn->query($feedbackQuery);

    while ($feedback = $feedbackResult->fetch_assoc()) {
        echo "<tr>
                <td>{$feedback['name']}</td>
                <td>{$feedback['email']}</td>
                <td>{$feedback['comment']}</td>
                <td>{$feedback['created_at']}</td>
                <td>
                    <form action='delete_feedback.php' method='POST'>
                        <input type='hidden' name='feedback_id' value='{$feedback['id']}'>
                        <button type='submit' class='delete-btn'>Delete</button>
                    </form>
                </td>
              </tr>";
    }
    ?>
</table>
</div>
<script>
function confirmLogout() {
    return confirm("Are you sure you want to logout?");
}
</script>

</body>
</html>
