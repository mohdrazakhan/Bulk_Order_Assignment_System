<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/logger_helper.php";

$db = new Database();
$conn = $db->connect();

try {
    // 1. Clear Assignments
    $conn->exec("TRUNCATE TABLE order_assignments");

    // 2. Reset Orders Status
    $conn->exec("UPDATE orders SET status='UNASSIGNED'");

    // 3. Reset Courier Counts
    $conn->exec("UPDATE couriers SET current_assigned_count=0");

    writeLog("System Reset: All assignments cleared.");

    echo json_encode(["message" => "Reset successful"]);
} catch (PDOException $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
