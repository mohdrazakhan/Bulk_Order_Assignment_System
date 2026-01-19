<?php
// header("Content-Type: application/json"); // Moved inside router logic or per-endpoint
require_once __DIR__ . '/../src/OrderService.php';
require_once __DIR__ . '/../src/CourierService.php';
require_once __DIR__ . '/../src/AssignmentService.php';
require_once __DIR__ . '/../src/SeedService.php';

// Simple Router
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize URI (remove project folder specific prefix if running in subdirectory, adaptable)
// For now, assume root or specifically /api/...
// Let's assume a basic switch for demo purposes.

// Helper to send response
function jsonResponse($data, $code = 200) {
    header("Content-Type: application/json");
    http_response_code($code);
    echo json_encode($data);
    exit;
}

try {
    if (($requestUri === '/' || $requestUri === '/dashboard') && $requestMethod === 'GET') {
        readfile(__DIR__ . '/dashboard.html');
        exit;
    }

    if ($requestUri === '/orders/unassigned' && $requestMethod === 'GET') {
        $location = $_GET['location'] ?? null;
        if (!$location) {
            jsonResponse(['error' => 'Missing location parameter'], 400);
        }
        $service = new OrderService();
        $orders = $service->getUnassignedOrders($location);
        jsonResponse($orders);

    } elseif ($requestUri === '/couriers/available' && $requestMethod === 'GET') {
        $location = $_GET['location'] ?? null;
        if (!$location) {
            jsonResponse(['error' => 'Missing location parameter'], 400);
        }
        $service = new CourierService();
        $couriers = $service->getAvailableCouriers($location);
        jsonResponse($couriers);

    } elseif ($requestUri === '/assignments/bulk' && $requestMethod === 'POST') {
        $service = new AssignmentService();
        $result = $service->runBulkAssignment();
        jsonResponse(['message' => 'Bulk assignment completed', 'details' => $result]);

    } elseif ($requestUri === '/system/reset' && $requestMethod === 'POST') {
        $service = new SeedService();
        $result = $service->resetDatabase();
        jsonResponse($result);

    } elseif ($requestUri === '/assignments/results' && $requestMethod === 'GET') {
        // Implement fetching results if needed, or just rely on database check
        // For demo, we can just return a placeholder or implement a method in AssignmentService
        jsonResponse(['message' => 'Check database table `assignments` for results']);
    } else {
        jsonResponse(['error' => 'Not Found', 'uri' => $requestUri], 404);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
