<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ReceiptOptima') }} - Pemindai Struk OCR</title>

        <!-- Google Fonts (Plus Jakarta Sans & Space Grotesk) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Theme Initialization Script (Prevents Screen Flash) -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        </script>

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                letter-spacing: -0.015em;
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            .font-display {
                font-family: 'Space Grotesk', sans-serif;
                letter-spacing: -0.025em;
            }
            .glow-blob {
                filter: blur(100px);
                opacity: 0.15;
            }

            /* Theme Dependent Colors */
            html.dark body {
                background-color: #030712;
                color: #e2e8f0;
            }
            html.light body {
                background-color: #f8fafc;
                color: #0f172a;
            }

            /* Card styles */
            html.dark .w-full.bg-slate-900\/40 {
                background-color: rgba(15, 23, 42, 0.4);
                border-color: rgba(255, 255, 255, 0.05);
            }
            html.light .w-full.bg-slate-900\/40 {
                background-color: rgba(255, 255, 255, 0.8);
                border-color: rgba(15, 23, 42, 0.06);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
            }

            /* Text & Input Overrides */
            html.light .text-white {
                color: #0f172a !important;
            }
            html.light .text-slate-400 {
                color: #475569 !important;
            }
            html.light label {
                color: #475569 !important;
            }
            html.light input {
                background-color: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #0f172a !important;
            }
            html.light input::placeholder {
                color: #94a3b8 !important;
            }
            html.light .border-slate-800\/50 {
                border-color: #cbd5e1 !important;
            }
        </style>
    </head>
    <body class="antialiased overflow-hidden min-h-screen relative flex items-center justify-center">
        
        <!-- Glowing Blobs -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-600 glow-blob pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 glow-blob pointer-events-none"></div>

        <div class="w-full sm:max-w-md px-6 relative z-10">
            <!-- Brand Logo Header -->
            <div class="flex flex-col items-center justify-center gap-3.5 mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center shadow-xl shadow-blue-500/20 relative group">
                    <span class="absolute inset-0 rounded-2xl bg-blue-400/20 blur opacity-70"></span>
                    <svg class="w-7 h-7 text-white relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex flex-col items-center text-center">
                    <span class="text-2xl font-bold text-white tracking-tight font-display">Receipt<span class="text-blue-500">Optima</span></span>
                    <span class="text-[9px] text-slate-400 font-extrabold tracking-widest uppercase mt-0.5 font-display">PEMINDAI STRUK & ANALISIS KEUANGAN</span>
                </div>
            </div>

            <!-- Main Auth Glassmorphic Card -->
            <div class="w-full bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 p-6 sm:p-8 rounded-3xl shadow-2xl relative overflow-hidden">
                <span class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-xl"></span>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
