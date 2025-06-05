<?php
session_start();
include 'connectDb.php'; // Ensure correct database connection

$DB = new DataBase();
$conn = $DB->connect();

function generateKeyword() {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 4);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone_number = trim($_POST['pNumber']);
    $address = trim($_POST['address']);
    $email = trim($_POST['e_mail']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $keyword = generateKeyword();

    // Check if passwords match
    if ($password !== $password2) {
        die("Passwords do not match. Please try again.");
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        die("Error: This email is already registered. Please use a different email.");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database using prepared statement
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone_number, address, email, password, keyword) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $first_name, $last_name, $phone_number, $address, $email, $hashed_password, $keyword);

    if ($stmt->execute()) {
        // Get the newly created user ID
        $user_id = $stmt->insert_id;

        // Redirect to index.php as a logged-in user
        header("Location: http://localhost/syndicate/");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
