<?php
require 'classes/connectDb.php'; // Include the database class

$db = new DataBase(); // Create an instance of DataBase
$conn = $db->connect(); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['e_mail']);
    $new_password = trim($_POST['new_password']);

    if (!empty($email) && !empty($new_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password in database
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_password, $email);

            if ($update_stmt->execute()) {
                echo "Password reset successful. <a href='login.php'>Log in</a>";
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "Email not found.";
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>
