<?php
class Courier {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllActive() {
        $stmt = $this->conn->prepare("SELECT * FROM couriers");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAssignmentCount($courierId) {
        $stmt = $this->conn->prepare("UPDATE couriers SET current_assigned_count = current_assigned_count + 1 WHERE id = ?");
        return $stmt->execute([$courierId]);
    }
}
