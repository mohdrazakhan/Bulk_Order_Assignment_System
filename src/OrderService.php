<?php
require_once __DIR__ . '/Database.php';

class OrderService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get unassigned orders for a specific location.
     */
    public function getUnassignedOrders($location, $limit = 100) {
        $sql = "SELECT order_id, delivery_location, order_value, order_date 
                FROM orders 
                WHERE status = 'UNASSIGNED' 
                AND delivery_location = :location 
                ORDER BY order_date ASC -- FIFO
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get Locations that have unassigned orders.
     */
    public function getLocationsWithPendingOrders() {
        $sql = "SELECT DISTINCT delivery_location FROM orders WHERE status = 'UNASSIGNED'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = :status WHERE order_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['status' => $status, 'id' => $orderId]);
    }
}
