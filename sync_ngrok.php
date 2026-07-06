<?php
echo "Memulai sinkronisasi otomatis ke Telegram...\n";

// 1. Fetch active Ngrok tunnels
$ngrokResponse = @file_get_contents('http://127.0.0.1:4040/api/tunnels');
if (!$ngrokResponse) {
    die("Error: Ngrok tidak berjalan atau belum online di port 4040.\n");
}

$data = json_decode($ngrokResponse, true);
$publicUrl = null;

if (isset($data['tunnels'])) {
    foreach ($data['tunnels'] as $tunnel) {
        if ($tunnel['proto'] === 'https') {
            $publicUrl = $tunnel['public_url'];
            break;
        }
    }
}

if (!$publicUrl) {
    die("Error: Tidak dapat menemukan URL HTTPS dari Ngrok.\n");
}

echo "URL Terowongan Ngrok Baru Ditemukan: {$publicUrl}\n";

// 2. Update .env file
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    // Escape backslashes for regex string replacement
    $replacement = 'APP_URL="' . str_replace('\\', '\\\\', $publicUrl) . '"';
    $content = preg_replace('/^APP_URL=.*/m', $replacement, $content);
    file_put_contents($envPath, $content);
    echo "✓ APP_URL di .env telah diperbarui.\n";
} else {
    die("Error: File .env tidak ditemukan.\n");
}

// 3. Clear Laravel config cache
exec('php artisan config:clear', $output, $returnVar);
if ($returnVar === 0) {
    echo "✓ Laravel Config Cache berhasil dibersihkan.\n";
}

// 4. Register Webhook directly (to avoid slow Laravel framework boot times)
$envContent = parse_ini_file('.env');
$botToken = $envContent['TELEGRAM_BOT_TOKEN'] ?? '';

if (!empty($botToken)) {
    $webhookTarget = $publicUrl . '/api/telegram/webhook';
    $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url={$webhookTarget}";
    $webhookResult = @file_get_contents($apiUrl);

    if ($webhookResult) {
        $resultJson = json_decode($webhookResult, true);
        if ($resultJson['ok']) {
            echo "✓ SUKSES: Webhook Telegram berhasil disambungkan ke {$webhookTarget}\n";
        } else {
            echo "❌ GAGAL: Terjadi error saat mendaftarkan webhook: " . $resultJson['description'] . "\n";
        }
    }
} else {
    echo "Info: TELEGRAM_BOT_TOKEN kosong, lompati registrasi webhook.\n";
}
