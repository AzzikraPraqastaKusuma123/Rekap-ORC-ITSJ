<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Struk Berhasil</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Telegram WebApp JS -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #030712;
            color: #e2e8f0;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 antialiased">
    
    <div class="w-full max-w-md bg-slate-900/40 backdrop-blur-xl border border-slate-800 p-8 rounded-3xl text-center space-y-6 shadow-2xl">
        <!-- Success Icon -->
        <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto text-emerald-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div>
            <h1 class="text-xl font-bold text-white tracking-wide">Koreksi Berhasil!</h1>
            <p class="text-xs text-slate-400 mt-2">Data struk belanja Anda telah berhasil diperbarui dan disinkronkan di dasbor admin.</p>
        </div>

        <div>
            <button onclick="closeWebApp()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-500/10">
                Tutup Halaman
            </button>
        </div>
    </div>

    <script>
        // Initialize Telegram WebApp
        Telegram.WebApp.ready();
        
        // Show Telegram alert popup and close upon user acknowledgement
        Telegram.WebApp.showAlert("Struk belanja berhasil diperbarui!", function() {
            Telegram.WebApp.close();
        });

        function closeWebApp() {
            Telegram.WebApp.close();
        }
    </script>
</body>
</html>
