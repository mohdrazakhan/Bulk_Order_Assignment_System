<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Courier.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Assignment.php';
require_once __DIR__ . '/logger_helper.php';

try {
    $database = new Database();
    $db = $database->connect();

    $courierObj = new Courier($db);
    $orderObj = new Order($db);
    $assignmentObj = new Assignment($db);

    // Fetch data
    $orders = $orderObj->getUnassigned();
    $couriers = $courierObj->getAllActive();

    $assignedCount = 0;
    
    // Start Transaction for data integrity
    $db->beginTransaction();

    foreach ($orders as $order) {
        $assigned = false;

        foreach ($couriers as &$courier) { // Pass by reference to update local count
            // Check Location Match
            $locations = explode(',', $courier['serviceable_locations']);
            $locations = array_map('trim', $locations); // cleanup whitespace
            
            if (in_array($order['delivery_location'], $locations)) {
                
                // Check Capacity Logic
                if ($courier['current_assigned_count'] < $courier['daily_capacity']) {
                    
                    // Assign Order
                    $assignmentObj->create($order['order_id'], $courier['id']);
                    $courierObj->updateAssignmentCount($courier['id']);
                    $orderObj->updateStatus($order['order_id']);

                    // Update local courier array to prevent over-assignment in this loop
                    $courier['current_assigned_count']++;
                    $assignedCount++;
                    $assigned = true;
                    break; // Move to next order
                }
            }
        }
    }

    writeLog("Run Assignment: $assignedCount new orders assigned.");

    $db->commit();
    echo json_encode(['success' => true, 'message' => "$assignedCount orders assigned successfully"]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
