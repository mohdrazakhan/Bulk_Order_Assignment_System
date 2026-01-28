<?php
function writeLog($message) {
    $logFile = __DIR__ . '/../logs/system.log';
    $timestamp = date("Y-m-d H:i:s");
    $entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}
