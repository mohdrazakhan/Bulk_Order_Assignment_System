<?php
class Database {
    private $host = "localhost";
    private $db_name = "bulk_order_system";
    private $username = "root";
    private $password = "root";
    private $port = "8889";
    public $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo "DB Connection Error: " . $e->getMessage();
            exit;
        }
    }
}
