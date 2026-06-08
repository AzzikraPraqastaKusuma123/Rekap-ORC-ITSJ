<?php
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $matched = 0;
    foreach ($lines as $line) {
        if (str_contains($line, '2026-06-08')) {
            echo $line;
            $matched++;
            if ($matched > 150) {
                break;
            }
        }
    }
    if ($matched === 0) {
        echo "No logs found for 2026-06-08\n";
    }
} else {
    echo "Log file not found.";
}
