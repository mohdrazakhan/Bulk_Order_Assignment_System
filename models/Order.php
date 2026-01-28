<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUnassigned() {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE status = 'UNASSIGNED'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($orderId) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = 'ASSIGNED' WHERE order_id = ?");
        return $stmt->execute([$orderId]);
    }
}
