<?php
$token = '8683625411:AAHJWrDtklcN5nYyyPN3jvdr1OR2RzlXeGM';
$url = 'https://de4d-2001-448a-20a2-8d29-3406-c613-421f-b26c.ngrok-free.app/api/telegram/webhook';
$ch = curl_init("https://api.telegram.org/bot{$token}/setWebhook");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['url' => $url]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
echo "Webhook result: " . $result;
