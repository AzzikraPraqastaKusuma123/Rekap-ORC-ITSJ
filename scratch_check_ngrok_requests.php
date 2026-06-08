<?php
$res = file_get_contents('http://127.0.0.1:4040/api/requests');
$data = json_decode($res, true);

if (isset($data['requests'])) {
    echo "Total requests: " . count($data['requests']) . "\n";
    foreach ($data['requests'] as $req) {
        echo "Time: " . $req['start_time'] . " | Method: " . $req['request']['method'] . " | Path: " . $req['request']['uri'] . " | Status: " . $req['response']['status_code'] . " | Duration: " . $req['duration'] . "ms\n";
    }
} else {
    echo "No requests found in ngrok inspector.\n";
}
