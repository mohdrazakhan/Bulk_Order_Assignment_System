<?php
class Assignment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($orderId, $courierId) {
        $stmt = $this->conn->prepare(
            "INSERT INTO order_assignments (order_id, agent_id)
             VALUES (?, ?)"
        );
        return $stmt->execute([$orderId, $courierId]);
    }
}
