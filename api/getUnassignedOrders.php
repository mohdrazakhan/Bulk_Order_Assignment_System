<?php
require_once __DIR__ . "/../config/db.php";

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT * FROM orders WHERE status='UNASSIGNED'");
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
