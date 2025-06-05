<?php
if (!class_exists('DataBase')) {
    class DataBase {
        private $host = "localhost";
        private $user = "root";
        private $password = "";
        private $dbname = "profile_db";
        public $conn;

        public function connect() {
            $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            return $this->conn;
        }

        public function save($query) {
            $conn = $this->connect();
            if ($conn->query($query) === TRUE) {
                return true;
            } else {
                die("Error executing query: " . $conn->error);
            }
        }

        public function read($query) {
            $conn = $this->connect();
            $result = $conn->query($query);

            if ($result === FALSE) {
                die("Error executing query: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                return [];
            }
        }
    }
}
?>
