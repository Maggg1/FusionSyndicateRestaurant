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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']); // Ensure it's an integer

    // Use prepared statement to delete securely
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback deleted successfully!'); window.location.href='adminprofile.php';</script>";
    } else {
        echo "<script>alert('Error deleting feedback.'); window.location.href='adminprofile.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
