<?php
session_start();
include 'connectDb.php'; // Ensure correct database connection

$DB = new DataBase();
$conn = $DB->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $email = trim($_POST['e_mail']);
    $password = $_POST['password'];

    // Validate email and password
    if (empty($email) || empty($password)) {
        echo "<p style='color:red; text-align:center;'>Email and password are required!</p>";
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<p style='color:red; text-align:center;'>Database error: " . $conn->error . "</p>";
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];

            // Role-based session assignment
            $_SESSION['role'] = (str_contains($user['email'], '.admin')) ? 'admin' : 'customer';

            // Redirect based on user role
            if ($_SESSION['role'] === 'admin') {
                header("Location: http://localhost/syndicate/adminprofile.php");
            } else {
                header("Location: http://localhost/syndicate/customerprofile.php");
            }
            exit();
        } else {
            echo "<p style='color:red; text-align:center;'>Invalid password!</p>";
			echo "<p style='text-align:center;' ><a style='text-align:center;' href=http://localhost/syndicate/login.php >Click Here to Login</a></p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>User not found!</p>";
		echo "<p style='text-align:center;' ><a href=http://localhost/syndicate/login.php >Click Here to Login</a></p>";
    }

    $stmt->close();
}

$conn->close();
?>