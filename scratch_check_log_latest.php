<?php
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -150);
    echo implode("", $lastLines);
} else {
    echo "Log file not found.";
}
