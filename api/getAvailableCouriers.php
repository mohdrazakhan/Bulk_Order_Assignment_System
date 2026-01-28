<?php
require_once __DIR__ . "/../config/db.php";

$db = new Database();
$conn = $db->connect();

$sql = "
SELECT id, name, serviceable_locations, daily_capacity,
       current_assigned_count,
       (daily_capacity - current_assigned_count) AS available_capacity
FROM couriers
";

$stmt = $conn->prepare($sql);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
