<?php
require_once __DIR__ . '/../src/Database.php';

$pdo = Database::getInstance()->getConnection();

echo "Seeding Database...\n";

// Clear Clean
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("TRUNCATE TABLE assignments");
$pdo->exec("TRUNCATE TABLE orders");
$pdo->exec("TRUNCATE TABLE courier_locations");
$pdo->exec("TRUNCATE TABLE couriers");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");

// 1. Create Couriers
$locations = ['Zone A', 'Zone B', 'Zone C'];
for ($i = 1; $i <= 10; $i++) {
    $name = "Courier $i";
    $capacity = 10;
    $stmt = $pdo->prepare("INSERT INTO couriers (name, daily_capacity) VALUES (?, ?)");
    $stmt->execute([$name, $capacity]);
    $courierId = $pdo->lastInsertId();

    // Assign random location
    $loc = $locations[array_rand($locations)];
    $stmt = $pdo->prepare("INSERT INTO courier_locations (courier_id, location) VALUES (?, ?)");
    $stmt->execute([$courierId, $loc]);
}
echo "Created 10 Couriers.\n";

// 2. Create Orders
for ($i = 1; $i <= 100; $i++) {
    $value = rand(100, 5000);
    $loc = $locations[array_rand($locations)];
    $date = date('Y-m-d H:i:s', strtotime("-$i minutes"));
    $stmt = $pdo->prepare("INSERT INTO orders (delivery_location, order_value, order_date, status) VALUES (?, ?, ?, 'UNASSIGNED')");
    $stmt->execute([$loc, $value, $date]);
}
echo "Created 100 Orders.\n";

echo "Seeding Complete.\n";
