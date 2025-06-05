<?php
include 'classes/connectDb.php'; // Ensure correct database connection

if (!class_exists('user')) {
    class user {
        public function get_data($id) {
            $DB = new DataBase();
            $conn = $DB->connect();

            $query = "SELECT * FROM users WHERE userid = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $result ?: false;
        }
    }
}
?>
