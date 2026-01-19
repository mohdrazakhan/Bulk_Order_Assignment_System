<?php
require_once __DIR__ . '/Database.php';

class CourierService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get available couriers for a specific location who have capacity.
     */
    public function getAvailableCouriers($location) {
        $sql = "
            SELECT c.id, c.name, c.daily_capacity, c.current_assigned_count 
            FROM couriers c
            JOIN courier_locations cl ON c.id = cl.courier_id
            WHERE cl.location = :location
            AND c.current_assigned_count < c.daily_capacity
            ORDER BY c.current_assigned_count ASC -- Load balancing strategy: pick least busy first
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['location' => $location]);
        return $stmt->fetchAll();
    }

    /**
     * Increment the assigned count for a courier.
     * Returns true if successful, false if capacity exceeded (race condition check).
     */
    public function incrementAssignment($courierId) {
        $sql = "UPDATE couriers 
                SET current_assigned_count = current_assigned_count + 1 
                WHERE id = :id AND current_assigned_count < daily_capacity";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $courierId]);
        return $stmt->rowCount() > 0;
    }
}
