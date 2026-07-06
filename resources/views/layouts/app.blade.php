<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a" id="theme-meta-color">

    <title>{{ config('app.name', 'ReceiptOptima') }} — Pemindai Struk & Rekap Keuangan</title>
    <meta name="description"
        content="ReceiptOptima — Sistem pemindai struk belanja otomatis berbasis AI dan Telegram OCR untuk rekap keuangan instan.">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ===== CRITICAL MOBILE LAYOUT CSS (inline - bypass build cache) ===== -->
    <style>
        /* Hide Alpine.js elements before JS loads */
        [x-cloak] {
            display: none !important;
        }

        /* Desktop sidebar: ALWAYS hidden on mobile, shown on desktop */
        #app-sidebar {
            display: none !important;
        }

        @media (min-width: 768px) {
            #app-sidebar {
                display: flex !important;
            }
        }

        /* Mobile bottom nav: ALWAYS hidden on desktop, shown on mobile */
        #mobile-bottom-nav {
            display: none !important;
        }

        @media (max-width: 767px) {
            #mobile-bottom-nav {
                display: flex !important;
            }
        }

        /* Mobile main content: add bottom padding for nav bar */
        @media (max-width: 767px) {
            main.has-bottom-nav {
                padding-bottom: calc(64px + env(safe-area-inset-bottom, 12px) + 8px) !important;
            }

            #app-header {
                height: 52px !important;
                min-height: 52px !important;
            }
        }
    </style>

    <!-- ===== DYNAMIC ROLE ACCENT THEME ===== -->
    <style>
        :root {
            --brand-primary:
                {{ $roleTheme['brandPrimary'] }}
            ;
            --brand-primary-hover:
                {{ $roleTheme['brandHover'] }}
            ;
        }
    </style>

    <!-- Prevent FOUC (Flash of Unstyled Content) - Theme init -->
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = stored || (prefersDark ? 'dark' : 'light');
            document.documentElement.classList.add(theme);
            document.documentElement.classList.remove(theme === 'dark' ? 'light' : 'dark');
            var metaColor = document.getElementById('theme-meta-color');
            if (metaColor) metaColor.content = theme === 'dark' ? '#0f172a' : '#f8fafc';
        })();
    </script>

    @stack('head')
</head>

<body class="h-full antialiased overflow-x-hidden" x-data="{ 
        mobileSidebarOpen: false,
        currentTheme: localStorage.getItem('theme') || 'dark',
        isGlobalLoading: true
    }" @@start-global-loading.window="isGlobalLoading = true" @@stop-global-loading.window="isGlobalLoading = false">

    <!-- ===== GLOBAL LOADING SCREEN ===== -->
    <div x-show="isGlobalLoading" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-md">

        <div class="relative flex items-center justify-center mb-6">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-2xl {{ $roleTheme['svgBg'] }} animate-ping"></div>
            <!-- Center Logo -->
            <div
                class="relative z-10 w-20 h-20 bg-white dark:bg-slate-800 rounded-2xl shadow-xl flex items-center justify-center border border-slate-200 dark:border-slate-700 animate-pulse">
                <img src="{{ asset('logo.png') }}" class="w-12 h-12 object-contain" alt="Optima Logo">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 {{ $roleTheme['bg'] }} rounded-full animate-bounce"></span>
            <span class="w-2.5 h-2.5 {{ $roleTheme['bg'] }} rounded-full animate-bounce"
                style="animation-delay: 0.15s;"></span>
            <span class="w-2.5 h-2.5 {{ $roleTheme['bg'] }} rounded-full animate-bounce"
                style="animation-delay: 0.3s;"></span>
        </div>
        <div class="mt-4 text-sm font-semibold text-slate-500 font-display animate-pulse tracking-widest uppercase">
            Memuat Sistem...
        </div>
    </div>
    <div class="fixed inset-0 z-40 bg-slate-900/40 md:hidden" x-show="mobileSidebarOpen" x-cloak
        @click="mobileSidebarOpen = false" @touchstart="mobileSidebarOpen = false" x-transition.opacity.duration.200ms>
    </div>

    <!-- ===== MOBILE SIDEBAR DRAWER ===== -->
    <div class="fixed inset-y-0 left-0 z-50 w-[280px] bg-white dark:bg-slate-900 shadow-2xl flex flex-col md:hidden"
        x-show="mobileSidebarOpen" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full">

        <!-- Brand -->
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-800 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('Logo app black.png') }}" alt="Logo ITSJ OCR"
                    class="w-44 h-auto object-contain drop-shadow-sm dark:hidden">
                <img src="{{ asset('Logo app.png') }}" alt="Logo ITSJ OCR Dark"
                    class="w-44 h-auto object-contain drop-shadow-sm hidden dark:block">
            </a>
            <button type="button" @click="mobileSidebarOpen = false" @touchstart="mobileSidebarOpen = false"
                class="icon-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Nav Links -->
        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            @include('layouts.navigation')
        </nav>

        <!-- Mobile User Panel -->
        <div class="mt-auto p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-bold text-white shrink-0 shadow-md {{ $roleTheme['bg'] }} transition-transform hover:scale-105 duration-200">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}
                    </div>
                    <div class="text-xs text-slate-500 capitalize truncate">{{ Auth::user()->role ?? 'user' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="icon-btn text-slate-500 hover:text-red-500" title="Keluar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== MAIN LAYOUT WRAPPER ===== -->
    <div class="min-h-screen flex relative z-10">

        <!-- ===== DESKTOP SIDEBAR ===== -->
        <aside id="app-sidebar" class="hidden md:flex flex-col w-64 xl:w-72 shrink-0 sticky top-0 h-screen z-30">

            <!-- Brand Header -->
            <div class="flex items-center justify-center px-6 py-4 border-b border-slate-200 dark:border-slate-800 shrink-0 w-full hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
                onclick="window.location='{{ route('dashboard') }}'">
                <img src="{{ asset('Logo app black.png') }}" alt="Logo ITSJ OCR Light"
                    class="w-56 h-auto object-contain drop-shadow-sm dark:hidden">
                <img src="{{ asset('Logo app.png') }}" alt="Logo ITSJ OCR Dark"
                    class="w-56 h-auto object-contain drop-shadow-sm hidden dark:block">
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="nav-link flex items-center gap-3.5 px-4 py-3 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                    <span class="w-6 h-6 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                    </span>
                    <span class="font-display">Dashboard Utama</span>
                </a>

                <!-- Receipts -->
                <a href="{{ route('receipts.index') }}"
                    class="nav-link flex items-center gap-3.5 px-4 py-3 text-sm font-semibold {{ request()->routeIs('receipts.*') ? 'nav-link-active' : '' }}">
                    <span class="w-6 h-6 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                    <span class="font-display">
                        @if(auth()->user()->role === 'admin')
                            Daftar Persetujuan
                        @else
                            Riwayat Struk
                        @endif
                    </span>
                </a>

                @if(auth()->user()->role === 'superadmin')
                    <!-- User Management -->
                    <a href="{{ route('users.index') }}"
                        class="nav-link flex items-center gap-3.5 px-4 py-3 text-sm font-semibold {{ request()->routeIs('users.index') ? 'nav-link-active' : '' }}">
                        <span class="w-6 h-6 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <span class="font-display">Manajemen Staf</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings') }}"
                        class="nav-link flex items-center gap-3.5 px-4 py-3 text-sm font-semibold {{ request()->routeIs('settings') ? 'nav-link-active' : '' }}">
                        <span class="w-6 h-6 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span class="font-display">Pengaturan</span>
                    </a>
                @endif

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}"
                    class="nav-link flex items-center gap-3.5 px-4 py-3 text-sm font-semibold {{ request()->routeIs('profile.edit') ? 'nav-link-active' : '' }}">
                    <span class="w-6 h-6 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <span class="font-display">Profil Saya</span>
                </a>

                <!-- Divider -->
                <div class="my-6 border-t border-slate-200 dark:border-slate-800"></div>

                <!-- Bot Status Info -->
                <div
                    class="px-4 py-4 rounded-xl text-xs space-y-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <div class="font-bold uppercase tracking-wider text-[10px] text-slate-500 font-display">Status
                        Sistem</div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Bot Telegram</span>
                        @if(!empty(env('TELEGRAM_BOT_TOKEN')))
                            <span class="badge badge-success">Terhubung</span>
                        @else
                            <span class="badge badge-warning">Belum Diset</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">AI OCR Engine</span>
                        <span class="badge badge-info">Online</span>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Footer User -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-sm {{ $roleTheme['bg'] }} transition-transform hover:scale-105 duration-200">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate font-display">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-[10px] text-slate-500 capitalize truncate">
                            {{ Auth::user()->role ?? 'Administrator' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="icon-btn hover:!text-red-500" title="Logout">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT AREA ===== -->
        <div class="flex-1 flex flex-col min-w-0">

            @if(\Illuminate\Support\Facades\Cache::get('gemini_quota_exhausted'))
                <!-- Spam Notification Script -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        function fireSpam() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'SISTEM MAINTENANCE',
                                    html: '<b>🚨 Maintenance Terjadwal (Kuota API Habis)</b><br>Sistem OCR AI sedang tidak dapat membaca struk (Limit Tercapai).<br><br>Harap segera perbarui kunci API Gemini di Menu Pengaturan!',
                                    confirmButtonText: 'Tutup Peringatan',
                                    confirmButtonColor: '#ef4444',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    backdrop: `rgba(220, 38, 38, 0.7)`
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        setTimeout(fireSpam, 5000); // 5 Seconds SPAM loop!
                                    }
                                });
                            }
                        }
                        setTimeout(fireSpam, 1500); // Initial pop up
                    });
                </script>

                <div
                    class="bg-red-600 text-white p-3 md:p-4 text-center text-sm font-bold shadow-lg shadow-red-500/20 z-50 rounded-b-xl mx-4 lg:mx-8 animate-pulse">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="uppercase tracking-wider">⚠️ PERINGATAN SISTEM: Kuota API Gemini Anda telah HABIS
                            (Error 429)! Harap segera perbarui kunci API di Menu Pengaturan!</span>
                    </div>
                </div>
            @endif

            <!-- ===== TOP HEADER ===== -->
            <header id="app-header"
                class="sticky top-0 z-20 flex items-center justify-between px-4 md:px-8 h-14 md:h-16 shrink-0">

                <!-- Left Side -->
                <div class="flex items-center gap-3">
                    <!-- Mobile Menu Button -->
                    <button type="button" @click="mobileSidebarOpen = true" @touchstart="mobileSidebarOpen = true"
                        class="md:hidden icon-btn">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Mobile Brand (shown only on mobile) -->
                    <a href="{{ route('dashboard') }}" class="flex items-center md:hidden pt-1">
                        <img src="{{ asset('Logo app black.png') }}" alt="Logo Light"
                            class="w-36 h-auto object-contain dark:hidden">
                        <img src="{{ asset('Logo app.png') }}" alt="Logo Dark"
                            class="w-36 h-auto object-contain hidden dark:block">
                    </a>

                    <!-- Desktop: Page breadcrumb -->
                    <div class="hidden md:flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-xs text-slate-500 font-semibold tracking-wider uppercase font-display">Sistem
                            Online</span>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Date (desktop) -->
                    <div
                        class="hidden lg:block text-xs text-slate-500 font-medium px-4 py-2 rounded-lg bg-slate-50 dark:bg-slate-800">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" type="button" class="icon-btn" title="Ganti Tema">
                        <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg id="icon-moon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- User Avatar (desktop) -->
                    <div class="hidden md:flex items-center gap-2">
                        <div
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold bg-blue-600 text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- ===== FLASH MESSAGES ===== -->
            @if(session('success') || session('error') || session('warning') || session('info'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        if (typeof Swal !== 'undefined') {
                            @if(session('success'))
                                Swal.fire({
                                    icon: 'success',
                                    title: 'BERHASIL!',
                                    html: '{!! nl2br(e(session('success'))) !!}',
                                    confirmButtonText: 'Oke, Mengerti',
                                    confirmButtonColor: 'var(--brand-primary, #3b82f6)',
                                    timer: 4500,
                                    timerProgressBar: true
                                });
                            @endif

                            @if(session('error'))
                                Swal.fire({
                                    icon: 'error',
                                    title: 'GAGAL / ERROR!',
                                    html: '{!! nl2br(e(session('error'))) !!}',
                                    confirmButtonText: 'Tutup',
                                    confirmButtonColor: '#ef4444'
                                });
                            @endif

                            @if(session('warning'))
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'PERHATIAN!',
                                    html: '{!! nl2br(e(session('warning'))) !!}',
                                    confirmButtonText: 'Mengerti',
                                    confirmButtonColor: '#f59e0b'
                                });
                            @endif

                            @if(session('info'))
                                Swal.fire({
                                    icon: 'info',
                                    title: 'INFORMASI',
                                    html: '{!! nl2br(e(session('info'))) !!}',
                                    confirmButtonText: 'Tutup',
                                    confirmButtonColor: '#3b82f6'
                                });
                            @endif
                                                                                                }
                    });
                </script>
            @endif
            <!-- ===== MAIN SLOT ===== -->
            <main class="flex-1 p-4 md:p-8 has-bottom-nav">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- ===== MOBILE BOTTOM NAVIGATION ===== -->
    <nav id="mobile-bottom-nav" class="md:hidden">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
            </span>
            <span>Beranda</span>
        </a>
        <!-- Receipts -->
        <a href="{{ route('receipts.index') }}"
            class="bottom-nav-item {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
            <span class="nav-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </span>
            <span>Struk</span>
        </a>

        <!-- Central Scan/Upload FAB -->
        @if(auth()->check() && auth()->user()->role === 'staff')
            <div class="bottom-nav-fab-wrap">
                <button type="button" @click="$dispatch('open-upload-modal')" class="bottom-nav-fab"
                    aria-label="Upload Struk Baru">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Settings -->
        <a href="{{ route('settings') }}" class="bottom-nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="nav-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            <span>Pengaturan</span>
        </a>
        <!-- Profile -->
        <a href="{{ route('profile.edit') }}"
            class="bottom-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <span class="nav-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </span>
            <span>Profil</span>
        </a>
    </nav>

    @stack('scripts')

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.getElementById('theme-toggle');
            var iconSun = document.getElementById('icon-sun');
            var iconMoon = document.getElementById('icon-moon');
            var metaColor = document.getElementById('theme-meta-color');

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
                    if (metaColor) metaColor.content = next === 'dark' ? '#0f172a' : '#f8fafc';
                    syncIcons();
                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: next } }));
                });
            }

            // Auto-dismiss alerts after 5s
            setTimeout(function () {
                document.querySelectorAll('[x-data*="show"]').forEach(function (el) {
                    if (el.__x) el.__x.$data.show = false;
                });
            }, 5000);

            // Global Loader handling
            window.onload = function () {
                // Sembunyikan loading screen ketika seluruh aset (gambar, css) selesai dimuat
                window.dispatchEvent(new CustomEvent('stop-global-loading'));
            };

            window.addEventListener('beforeunload', function () {
                // Tampilkan kembali loading screen ketika user menekan link / pindah halaman
                window.dispatchEvent(new CustomEvent('start-global-loading'));
            });
        });
    </script>

    @if(auth()->check() && auth()->user()->role === 'staff')
        @include('receipts.partials.upload-modal')
    @endif

</body>

</html>