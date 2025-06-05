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

// Handle form submissions for adding, updating, and deleting menu items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $image = $_POST['image'];
        $stmt = $conn->prepare("INSERT INTO menu_items (name, price, category, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $category, $image);
        $stmt->execute();
    } elseif (isset($_POST['update_item'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $image = $_POST['image'];
        $stmt = $conn->prepare("UPDATE menu_items SET name=?, price=?, category=?, image=? WHERE id=?");
        $stmt->bind_param("sdssi", $name, $price, $category, $image, $id);
        $stmt->execute();
    } elseif (isset($_POST['delete_item'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch menu items grouped by category
$result = $conn->query("SELECT * FROM menu_items ORDER BY category, name");
$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[$row['category']][] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Menu Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
            text-align: center;
        }
        h2, h3 {
            color: #333;
        }
        nav {
            background-color: #0073e6;
            padding: 10px;
            margin-bottom: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
        }
        nav a:hover {
            background-color: #005bb5;
        }
        form {
            display: inline-block;
            text-align: left;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        input, button {
            padding: 8px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #0073e6;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #005bb5;
        }
        table {
            width: 100%;
            max-width: 800px;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #0073e6;
            color: white;
        }
        .category-header {
            background-color: #005bb5;
            color: white;
            padding: 10px;
            font-size: 18px;
            text-align: left;
        }
    </style>
</head>
<body>

<nav>
    <a href="adminprofile.php">Admin Profile</a>
    <a href="index.php">Home</a>
</nav>

<h2>Manage Menu</h2>

<h3>Add New Item</h3>
<form method="POST">
    <input type="text" name="name" placeholder="Item Name" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="text" name="category" placeholder="Category" required>
    <input type="text" name="image" placeholder="Image Filename" required>
    <button type="submit" name="add_item">Add Item</button>
</form>

<h3>Existing Menu Items</h3>
<?php if (!empty($menu_items)): ?>
    <table>
        <?php foreach ($menu_items as $category => $items): ?>
            <tr>
                <th colspan="5" class="category-header"><?= htmlspecialchars($category) ?></th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <form method="POST">
                        <td><input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required></td>
                        <td><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required></td>
                        <td><input type="text" name="image" value="<?= htmlspecialchars($item['image']) ?>" required></td>
                        <td>
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="category" value="<?= htmlspecialchars($item['category']) ?>">
                            <button type="submit" name="update_item" onclick="return confirmUpdate()">Update</button>
                            <button type="submit" name="delete_item" onclick="return confirmDelete()">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No menu items available.</p>
<?php endif; ?>

<script>
function confirmUpdate() {
    return confirm("Are you sure you want to update this menu item?");
}
function confirmDelete() {
    return confirm("Are you sure you want to delete this menu item?");
}
</script>

</body>
</html>
