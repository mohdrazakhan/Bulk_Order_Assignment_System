<?php
$logFile = __DIR__ . '/../logs/system.log';

if (!file_exists($logFile)) {
    echo json_encode([]);
    exit;
}

$logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
// Get last 20 logs
$recentLogs = array_slice($logs, -20);

echo json_encode($recentLogs);
