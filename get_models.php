<?php
$apiKey = 'REDACTED_API_KEY';
$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
if (isset($data['error'])) {
    echo "API Error: " . $data['error']['message'] . "\n";
} elseif (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'gemini') !== false) {
            echo $model['name'] . "\n";
        }
    }
} else {
    echo "Unknown response: " . $response . "\n";
}
