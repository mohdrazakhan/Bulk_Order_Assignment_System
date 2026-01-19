<?php
require_once __DIR__ . '/Database.php';

class SeedService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function resetDatabase() {
        // Clear Tables
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $this->pdo->exec("TRUNCATE TABLE assignments");
        $this->pdo->exec("TRUNCATE TABLE orders");
        $this->pdo->exec("TRUNCATE TABLE courier_locations");
        $this->pdo->exec("TRUNCATE TABLE couriers");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        // 1. Create Couriers
        $locations = ['Zone A', 'Zone B', 'Zone C'];
        for ($i = 1; $i <= 10; $i++) {
            $name = "Courier $i";
            $capacity = rand(5, 15); // Random capacity for variety
            $stmt = $this->pdo->prepare("INSERT INTO couriers (name, daily_capacity) VALUES (?, ?)");
            $stmt->execute([$name, $capacity]);
            $courierId = $this->pdo->lastInsertId();

            // Assign random location
            $loc = $locations[array_rand($locations)];
            $stmt = $this->pdo->prepare("INSERT INTO courier_locations (courier_id, location) VALUES (?, ?)");
            $stmt->execute([$courierId, $loc]);
        }

        // 2. Create Orders
        for ($i = 1; $i <= 100; $i++) {
            $value = rand(100, 5000);
            $loc = $locations[array_rand($locations)];
            $date = date('Y-m-d H:i:s', strtotime("-$i minutes"));
            $stmt = $this->pdo->prepare("INSERT INTO orders (delivery_location, order_value, order_date, status) VALUES (?, ?, ?, 'UNASSIGNED')");
            $stmt->execute([$loc, $value, $date]);
        }

        return ['message' => 'Database reset with 100 new orders and 10 couriers.'];
    }
}
