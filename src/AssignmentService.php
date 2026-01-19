<?php
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/CourierService.php';

class AssignmentService {
    private $orderService;
    private $courierService;
    private $pdo;

    public function __construct() {
        $this->orderService = new OrderService();
        $this->courierService = new CourierService();
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function runBulkAssignment() {
        $locations = $this->orderService->getLocationsWithPendingOrders();
        $results = [
            'total_assigned' => 0,
            'errors' => []
        ];

        foreach ($locations as $location) {
            // Fetch batch of orders
            $orders = $this->orderService->getUnassignedOrders($location, 100); // Batch size 100
            if (empty($orders)) continue;

            $couriers = $this->courierService->getAvailableCouriers($location);
            if (empty($couriers)) {
                $results['errors'][] = "No couriers available for location: $location";
                continue;
            }

            // Simple Round-Robin or Fill-First?
            // "Fill-First" is easier with the DB atomic increment check.
            // But let's try to distribute a bit.
            // Actually, fetching fresh courier state inside loop is expensive.
            // We will iterate orders and try to assign to the first available courier in our list.
            // If `incrementAssignment` fails (race condition or full), we move to next courier.
            
            foreach ($orders as $order) {
                $assigned = false;
                foreach ($couriers as $key => $courier) {
                    try {
                        // Optimistic locking via UPDATE query
                        if ($this->courierService->incrementAssignment($courier['id'])) {
                            // Link Order to Courier
                            $this->createAssignment($order['order_id'], $courier['id']);
                            
                            // Update core order status
                            $this->orderService->updateStatus($order['order_id'], 'ASSIGNED');
                            
                            $assigned = true;
                            $results['total_assigned']++;
                            
                            // Update local courier count to avoid unnecessary DB hits if we know it's likely full?
                            // No, `incrementAssignment` handles safety.
                            // But we should check if we should remove this courier from our local list if we exceeded capacity locally 
                            // to save DB calls.
                            $couriers[$key]['current_assigned_count']++;
                            if ($couriers[$key]['current_assigned_count'] >= $courier['daily_capacity']) {
                                unset($couriers[$key]); // Remove full courier from local list
                            }
                            break; // Move to next order
                        } else {
                            // If update failed, it means courier is full (or row deleted).
                            unset($couriers[$key]);
                        }
                    } catch (Exception $e) {
                         $results['errors'][] = "Failed to assign Order {$order['order_id']}: " . $e->getMessage();
                    }
                }
                
                if (!$assigned) {
                    // Could not assign this order (no capacity left in any courier)
                    // We can stop processing this location to save resources, or continue if logic requires.
                    // For now, break, as no couriers are left.
                    if (empty($couriers)) break; 
                }
            }
        }
        
        return $results;
    }

    private function createAssignment($orderId, $courierId) {
        $sql = "INSERT INTO assignments (order_id, courier_id, status) VALUES (:oid, :cid, 'SUCCESS')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['oid' => $orderId, 'cid' => $courierId]);
    }
}
