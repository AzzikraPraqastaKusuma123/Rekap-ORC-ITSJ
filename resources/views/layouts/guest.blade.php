<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#030712" id="theme-meta-color">

    <title>{{ config('app.name', 'ReceiptOptima') }} — Masuk ke Dasbor</title>
    <meta name="description" content="Masuk ke ReceiptOptima — Platform rekap struk belanja otomatis berbasis AI.">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Prevent FOUC -->
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = stored || (prefersDark ? 'dark' : 'light');
            document.documentElement.classList.add(theme);
            document.documentElement.classList.remove(theme === 'dark' ? 'light' : 'dark');
        })();
    </script>

    <style>
        /* Auth page specific overrides */
        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Grid pattern overlay */
        .grid-pattern {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        html.light .grid-pattern {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
        }

        /* Auth input override */
        .auth-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html.dark .auth-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #f1f5f9;
        }

        html.dark .auth-input::placeholder {
            color: #475569;
        }

        html.dark .auth-input:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        html.light .auth-input {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        html.light .auth-input::placeholder {
            color: #94a3b8;
        }

        html.light .auth-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        /* Override Breeze input component styles */
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            outline: none !important;
            transition: all 0.2s ease !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        html.dark input[type="email"],
        html.dark input[type="password"],
        html.dark input[type="text"] {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f1f5f9 !important;
        }

        html.dark input[type="email"]::placeholder,
        html.dark input[type="password"]::placeholder,
        html.dark input[type="text"]::placeholder {
            color: #475569 !important;
        }

        html.dark input[type="email"]:focus,
        html.dark input[type="password"]:focus,
        html.dark input[type="text"]:focus {
            background: rgba(255, 255, 255, 0.07) !important;
            border-color: rgba(59, 130, 246, 0.5) !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08) !important;
        }

        html.light input[type="email"],
        html.light input[type="password"],
        html.light input[type="text"] {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light input[type="email"]:focus,
        html.light input[type="password"]:focus,
        html.light input[type="text"]:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08) !important;
        }

        /* Label override */
        html.dark label {
            color: #64748b !important;
        }

        html.light label {
            color: #475569 !important;
        }

        /* Error text */
        .text-red-600 {
            color: #f87171 !important;
        }

        html.light .text-red-600 {
            color: #ef4444 !important;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Grid Pattern Overlay -->
    <div class="grid-pattern"></div>

    <!-- Ambient Glow Blobs -->
    <div aria-hidden="true" class="pointer-events-none">
        <div class="glow-blob glow-blob-1 fixed w-[60%] h-[60%] top-[-20%] left-[-20%]" style="animation-delay:0s;">
        </div>
        <div class="glow-blob glow-blob-2 fixed w-[60%] h-[60%] bottom-[-20%] right-[-20%]" style="animation-delay:7s;">
        </div>
        <div class="glow-blob glow-blob-3 fixed w-[40%] h-[40%] top-[30%] left-[30%]" style="animation-delay:14s;">
        </div>
    </div>

    <!-- Theme Toggle (top right) -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle-guest" type="button" class="icon-btn w-9 h-9" title="Ubah Tema">
            <svg id="icon-sun-guest" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
            <svg id="icon-moon-guest" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <!-- Auth Container -->
    <div class="w-full max-w-sm px-4 py-8 relative z-10 animate-scale-in my-auto">

        <!-- Brand Logo -->
        <div class="flex flex-col items-center gap-2 mb-8">
            <div class="flex items-center justify-center animate-scale-in" style="animation-delay:0.1s;">
                <!-- Logo untuk Light Mode (Terang) -->
                <img src="{{ asset('Logo app black.png') }}"
                    class="w-56 md:w-64 h-auto object-contain drop-shadow-sm relative z-10 dark:hidden"
                    alt="Logo Aplikasi Padi Guard / ITSJ">
                <!-- Logo untuk Dark Mode (Gelap) -->
                <img src="{{ asset('Logo app.png') }}"
                    class="w-56 md:w-64 h-auto object-contain drop-shadow-sm relative z-10 hidden dark:block"
                    alt="Logo Aplikasi Padi Guard / ITSJ">
            </div>
            <div class="text-center animate-fade-in-up" style="animation-delay:0.15s;">
                <p class="text-[11px] font-black tracking-widest uppercase mt-2 text-slate-400 font-display">
                    Pemindai Struk &amp; Analisis Keuangan
                </p>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card p-7 animate-fade-in-up" style="animation-delay:0.2s;">
            <!-- Decorative shine top-right -->
            <span class="absolute top-0 right-0 w-32 h-32 rounded-full pointer-events-none"
                style="background:radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%);"></span>

            {{ $slot }}
        </div>

        <!-- Footer text -->
        <p class="text-center text-[11px] text-slate-500 mt-6 font-semibold animate-fade-in"
            style="animation-delay:0.3s;">
            &copy; {{ date('Y') }} PT. ITSJ Internal Tools
        </p>
    </div>

    <!-- Theme Toggle Script (Guest) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.getElementById('theme-toggle-guest');
            var iconSun = document.getElementById('icon-sun-guest');
            var iconMoon = document.getElementById('icon-moon-guest');

            function syncIcons() {
                var isDark = document.documentElement.classList.contains('dark');
                if (iconSun) iconSun.classList.toggle('hidden', !isDark);
                if (iconMoon) iconMoon.classList.toggle('hidden', isDark);
            }

            syncIcons();

            if (toggle) {
                toggle.addEventListener('click', function () {
                    var isDark = document.documentElement.classList.contains('dark');
                    var next = isDark ? 'light' : 'dark';
                    document.documentElement.classList.remove(isDark ? 'dark' : 'light');
                    document.documentElement.classList.add(next);
                    localStorage.setItem('theme', next);
                    syncIcons();
                });
            }
        });
    </script>
</body>

</html>