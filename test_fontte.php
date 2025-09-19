<?php

require_once 'vendor/autoload.php';

use App\Services\FontteService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test koneksi Fontte
$fontte = new FontteService();

echo "=== TEST KONEKSI FONTTE ===\n";
echo "API URL: " . config('services.fontte.url') . "\n";
echo "Token: " . substr(config('services.fontte.token'), 0, 10) . "...\n\n";

echo "1. Cek Device Status:\n";
$deviceResult = $fontte->checkDevice();
print_r($deviceResult);

echo "\n2. Test Kirim Pesan ke Nomor Test:\n";
$testResult = $fontte->sendMessage('6281234567890', 'Test pesan dari Laravel SPP');
print_r($testResult);

echo "\n=== SELESAI ===\n";