@echo off
title START REKAP OCR ITSJ
color 0A

echo ===================================================
echo    MEMULAI SISTEM REKAP OCR - PT. ITSJ (LOKAL)
echo ===================================================
echo.

REM 0. Mengoptimalkan Framework (Wuss! Sangat Cepat)
echo [0/4] Membersihkan Cache dan Mengoptimalkan Laravel...
call php artisan optimize:clear >nul
call php artisan optimize >nul

REM 1. Memulai Server Website (Berjalan di Minimalkan)
echo [1/4] Menyalakan Web Server Laravel (Port 8000)...
start "Laravel Web Server" /min cmd /c "php artisan serve"

REM 2. Memulai Queue / Mesin Antrean (Berjalan di Minimalkan)
echo [2/4] Menyalakan Mesin Pekerja OCR...
start "Laravel Queue Worker" /min cmd /c "php artisan queue:listen"

REM 3. Memulai Ngrok Terowongan (Berjalan di Minimalkan)
echo [3/4] Membuka Terowongan Ngrok...
start "Ngrok Tunnel" /min cmd /c "ngrok http 8000"

echo.
echo MENUNGGU NGROK ONLINE... (Mohon tunggu 8 detik)
timeout /t 8 /nobreak >nul

echo.
echo ===================================================
echo MENYINKRONKAN KONEKSI BOT TELEGRAM KE NGROK BARU...
echo ===================================================
php sync_ngrok.php

echo.
echo ===================================================
echo SEMUA SISTEM TELAH BERJALAN DENGAN SUKSES!
echo.
echo 1. Jangan tutup ke-3 jendela hitam kecil yang baru saja terbuka.
echo 2. Bot Telegram Anda sudah terhubung secara otomatis.
echo 3. Dasbor aplikasi kini siap diakses dan dipantau.
echo ===================================================
echo.
pause
