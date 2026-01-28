<?php
require_once __DIR__ . "/../config/db.php";

$db = new Database();
$conn = $db->connect();

$sql = "
SELECT oa.assignment_id, o.delivery_location,
       c.name AS courier, oa.assignment_date
FROM order_assignments oa
JOIN orders o ON oa.order_id = o.order_id
JOIN couriers c ON oa.agent_id = c.id
";

$stmt = $conn->prepare($sql);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
