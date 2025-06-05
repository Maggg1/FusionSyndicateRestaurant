<?php
session_start();
include 'classes/connectDb.php'; // Ensure the path is correct

// Initialize database connection
$db = new DataBase(); // Create an instance of the class
$conn = $db->connect(); // Connect to the database

if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    // Validate input (Basic security)
    if (empty($customer_id) || empty($name) || empty($email) || empty($comment)) {
        die("All fields are required.");
    }

    // Prepare and insert feedback into the database
    $sql = "INSERT INTO feedback (customer_id, name, email, comment, created_at) 
            VALUES (?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isss", $customer_id, $name, $email, $comment);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully!'); window.location.href='AboutUs.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }

    $conn->close();
} else {
    header("Location: AboutUs.php");
    exit();
}
?>
