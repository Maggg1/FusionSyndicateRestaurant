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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Update user details in the database
    $update_query = "UPDATE users SET first_name = ?, last_name = ?, address = ?, phone_number = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        echo "Database error: " . $conn->error;
        exit();
    }

    $update_stmt->bind_param("sssssi", $first_name, $last_name, $address, $phone, $email, $user_id);
    if ($update_stmt->execute()) {
        // Update session variables
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;

        // Redirect back to the profile page with a success message
        header("Location: customerprofile.php?success=1");
        exit();
    } else {
        echo "Error updating profile: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$conn->close();
?>