<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ReceiptOptima') }} - Pemindai Struk & Analisis</title>

        <!-- Google Fonts (Plus Jakarta Sans & Space Grotesk) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- TailwindCSS & AlpineJS (Laravel Breeze Asset Bundling) -->
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

        <!-- Inline Dark & Light Dashboard Core Styles -->
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
            
            /* Theme Dependent Background & Text Colors */
            html.dark body {
                background-color: #030712;
                color: #e2e8f0;
            }
            html.light body {
                background-color: #f8fafc;
                color: #0f172a;
            }
            
            /* Theme Dependent Card Styles */
            html.dark .glass-card {
                background: rgba(10, 15, 30, 0.5);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid rgba(255, 255, 255, 0.06);
                box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.4);
            }
            html.light .glass-card {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid rgba(15, 23, 42, 0.05);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            }
            
            html.dark .glass-card-hover:hover {
                background: rgba(15, 23, 42, 0.7);
                border-color: rgba(59, 130, 246, 0.25);
                box-shadow: 0 15px 35px rgba(59, 130, 246, 0.1);
            }
            html.light .glass-card-hover:hover {
                background: rgba(255, 255, 255, 0.9);
                border-color: rgba(59, 130, 246, 0.2);
                box-shadow: 0 12px 35px rgba(59, 130, 246, 0.08);
            }

            /* Sidebar active states glow markers */
            html.dark nav a.bg-blue-600\/10 {
                position: relative;
                background-color: rgba(59, 130, 246, 0.08);
                border-color: rgba(59, 130, 246, 0.25) !important;
                color: #60a5fa !important;
                box-shadow: inset 0 0 15px rgba(59, 130, 246, 0.05);
            }
            html.dark nav a.bg-blue-600\/10::before {
                content: '';
                position: absolute;
                left: -1px;
                top: 25%;
                height: 50%;
                width: 4.5px;
                border-radius: 9999px;
                background-color: #3b82f6;
                box-shadow: 0 0 10px #3b82f6, 0 0 20px #3b82f6;
            }

            /* Sidebar background */
            html.dark aside {
                background-color: rgba(7, 10, 18, 0.96);
            }
            html.light aside {
                background-color: #ffffff;
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.02);
            }
            
            /* Header */
            html.dark header {
                background-color: rgba(3, 7, 18, 0.6);
            }
            html.light header {
                background-color: rgba(255, 255, 255, 0.8);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
            }
            
            /* Glowing background blobs */
            html.dark .glow-blob {
                filter: blur(120px);
                opacity: 0.15;
                z-index: 0;
            }
            html.light .glow-blob {
                filter: blur(140px);
                opacity: 0.05;
                z-index: 0;
            }

            /* Navigation transitions */
            html.light nav a {
                color: #475569;
            }
            html.light nav a:hover {
                background-color: rgba(241, 245, 249, 0.8);
                color: #0f172a;
            }
            html.light nav a.bg-blue-600\/10 {
                background-color: rgba(59, 130, 246, 0.08);
                color: #2563eb !important;
                border-color: rgba(59, 130, 246, 0.15);
            }
            
            /* Table formatting */
            html.light table th {
                color: #475569;
                border-bottom-color: #e2e8f0;
            }
            html.light table tbody tr:hover {
                background-color: rgba(241, 245, 249, 0.5);
            }
            html.light table tbody td {
                color: #334155;
            }
            html.light table tbody td.text-white {
                color: #0f172a !important;
            }
            html.light code {
                background-color: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
            }

            /* Inputs & dropdowns in Light Mode */
            html.light input, html.light select {
                background-color: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #0f172a !important;
            }
            html.light input::placeholder, html.light select::placeholder {
                color: #94a3b8 !important;
            }
            html.light input:focus, html.light select:focus {
                border-color: #3b82f6 !important;
            }
            
            /* Modal style overrides */
            html.light .relative.glass-card.bg-slate-900\/95 {
                background-color: #ffffff !important;
                border-color: #e2e8f0 !important;
            }
            html.light .bg-slate-950\/60 {
                background-color: #f8fafc !important;
                border-color: #e2e8f0 !important;
            }
            html.light .bg-slate-950 {
                background-color: #f1f5f9 !important;
                border-color: #e2e8f0 !important;
                color: #334155 !important;
            }
            html.light .border-slate-800\/60, html.light .border-slate-800\/40, html.light .border-slate-800 {
                border-color: #e2e8f0 !important;
            }
            
            /* Labels, headings & high-specificity text overrides */
            html.light label {
                color: #475569 !important;
            }
            html.light h1, html.light h2, html.light h3, html.light h4 {
                color: #0f172a !important;
            }
            
            /* Target Tailwind's utility classes directly for maximum specificity */
            html.light .text-white {
                color: #0f172a !important;
            }
            html.light .text-slate-200 {
                color: #1e293b !important;
            }
            html.light .text-slate-300 {
                color: #334155 !important;
            }
            html.light .text-slate-400 {
                color: #475569 !important;
            }
            html.light .text-slate-500 {
                color: #64748b !important;
            }
            html.light .text-slate-600 {
                color: #475569 !important;
            }
            
            /* Custom background and container overrides */
            html.light .bg-slate-950, 
            html.light .bg-slate-950\/60,
            html.light .bg-slate-950\/80,
            html.light .bg-slate-950\/40 {
                background-color: #f8fafc !important;
            }
            html.light .bg-slate-900, 
            html.light .bg-slate-900\/60 {
                background-color: #f1f5f9 !important;
            }
            html.light .bg-\[\#090d16\]\/95 {
                background-color: #ffffff !important;
            }
            html.light .bg-\[\#030712\] {
                background-color: #f8fafc !important;
            }
            
            /* Border overrides */
            html.light .border-slate-800,
            html.light .border-slate-800\/80,
            html.light .border-slate-800\/60,
            html.light .border-slate-800\/40,
            html.light .border-slate-800\/50,
            html.light .border-slate-700\/40 {
                border-color: #cbd5e1 !important;
            }

            /* Keep white text white inside primary colored elements, buttons, badges, and logos */
            html.light .bg-blue-600,
            html.light .bg-blue-600 *,
            html.light .bg-blue-500,
            html.light .bg-blue-500 *,
            html.light .bg-red-600,
            html.light .bg-red-600 *,
            html.light .bg-emerald-600,
            html.light .bg-emerald-600 *,
            html.light .bg-gradient-to-tr,
            html.light .bg-gradient-to-tr *,
            html.light button[type="submit"],
            html.light button[type="submit"] *,
            html.light .text-white.bg-blue-600,
            html.light .bg-gradient-to-tr .text-white {
                color: #ffffff !important;
            }

            /* Light mode button & link adjustments */
            html.light a.text-blue-400, html.light button.text-blue-400 {
                color: #2563eb !important;
            }
            html.light a.text-blue-400:hover, html.light button.text-blue-400:hover {
                color: #1d4ed8 !important;
            }
            
            /* Riwayat Struk Export Buttons (CSV, PDF, Excel) */
            html.light button.border-slate-800.bg-slate-900\/60,
            html.light a.border-slate-800.bg-slate-900\/60 {
                background-color: #ffffff !important;
                border-color: #e2e8f0 !important;
                color: #475569 !important;
            }
            html.light button.border-slate-800.bg-slate-900\/60:hover,
            html.light a.border-slate-800.bg-slate-900\/60:hover {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }
            
            /* Reset filter button & Cancel/Batal modal button */
            html.light .bg-slate-800, html.light button.bg-slate-800 {
                background-color: #e2e8f0 !important;
                color: #475569 !important;
            }
            html.light .bg-slate-800:hover, html.light button.bg-slate-800:hover {
                background-color: #cbd5e1 !important;
                color: #0f172a !important;
            }
            
            /* Modal "+ Tambah Item" button */
            html.light button.bg-blue-600\/20 {
                background-color: rgba(59, 130, 246, 0.08) !important;
                color: #2563eb !important;
                border-color: rgba(59, 130, 246, 0.15) !important;
            }
            html.light button.bg-blue-600\/20:hover {
                background-color: #2563eb !important;
                color: #ffffff !important;
            }

            /* Delete row buttons / bin icons in light mode */
            html.light button.text-slate-500:hover {
                color: #ef4444 !important;
                background-color: rgba(239, 68, 68, 0.05) !important;
            }
            
            /* Sync Button inside receipt manual verification modal */
            html.light button.bg-slate-800.hover\:bg-slate-700 {
                background-color: #e2e8f0 !important;
                color: #475569 !important;
            }
            html.light button.bg-slate-800.hover\:bg-slate-700:hover {
                background-color: #cbd5e1 !important;
                color: #0f172a !important;
            }
            
            /* Pagination active/inactive buttons */
            html.light .pagination a, html.light nav[aria-label="Pagination Navigation"] a,
            html.light nav[aria-label="Pagination Navigation"] span, html.light nav[aria-label="Pagination Navigation"] button {
                background-color: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #334155 !important;
            }
            html.light nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
                background-color: rgba(59, 130, 246, 0.08) !important;
                color: #2563eb !important;
                border-color: rgba(59, 130, 246, 0.15) !important;
            }
            
            /* Status Indicators (Webhook / Chat ID Connection cards) */
            html.light .bg-slate-900.border-slate-800 {
                background-color: #e2e8f0 !important;
                border-color: #cbd5e1 !important;
                color: #475569 !important;
            }
            
            /* Save Settings Button - ensure shadow looks smooth in light mode */
            html.light button.shadow-lg {
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12) !important;
            }

            /* Premium scrollbars */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #090d16;
            }
            ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #3b82f6;
            }
        </style>

        <!-- Optional Dashboard Script hooks (e.g. ApexCharts) -->
        @stack('head')
    </head>
    <body class="h-full text-slate-200 antialiased overflow-x-hidden" x-data="{ mobileSidebarOpen: false }">
        
        <!-- Glowing Ambient Background Blobs -->
        <div class="fixed top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-600 glow-blob pointer-events-none"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 glow-blob pointer-events-none"></div>
        <div class="fixed top-[40%] left-[60%] w-[35%] h-[35%] rounded-full bg-cyan-600 glow-blob pointer-events-none"></div>

        <div class="min-h-screen flex flex-col md:flex-row relative z-10">
            
            <!-- ====== SIDEBAR NAVIGATION ====== -->
            <aside class="w-full md:w-64 bg-[#090d16]/95 border-b md:border-b-0 md:border-r border-slate-800/80 flex flex-col shrink-0 transition-all duration-300 md:block" 
                   :class="mobileSidebarOpen ? 'block' : 'hidden'">
                
                <!-- Brand / Logo Header -->
                <div class="p-6 flex items-center justify-between border-b border-slate-800/60 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <!-- Futuristic Glowing Hexagon Logo -->
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/20 relative group">
                            <span class="absolute inset-0 rounded-xl bg-blue-400/20 blur opacity-70 transition group-hover:opacity-100"></span>
                            <svg class="w-5 h-5 text-white relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-base font-bold text-white tracking-tight font-display">Receipt<span class="text-blue-500">Optima</span></span>
                            <span class="text-[9px] text-slate-400 font-extrabold tracking-widest uppercase font-display">PEMINDAI STRUK & ANALISIS</span>
                        </div>
                    </a>
                    
                    <!-- Close button mobile sidebar -->
                    <button class="md:hidden text-slate-400 hover:text-white" @click="mobileSidebarOpen = false">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Nav Links -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto font-display">
                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold tracking-wide transition-all group {{ request()->routeIs('dashboard') ? 'bg-blue-600/10 border border-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white border border-transparent' }}">
                        <svg class="w-5 h-5 transition group-hover:scale-105" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Dashboard Utama
                    </a>

                    <!-- Receipts Link -->
                    <a href="{{ route('receipts.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold tracking-wide transition-all group {{ request()->routeIs('receipts.*') ? 'bg-blue-600/10 border border-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white border border-transparent' }}">
                        <svg class="w-5 h-5 transition group-hover:scale-105" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Riwayat Galeri
                    </a>

                    <!-- Settings Link -->
                    <a href="{{ route('settings') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold tracking-wide transition-all group {{ request()->routeIs('settings') ? 'bg-blue-600/10 border border-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white border border-transparent' }}">
                        <svg class="w-5 h-5 transition group-hover:scale-105" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan Sistem
                    </a>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold tracking-wide transition-all group {{ request()->routeIs('profile.edit') ? 'bg-blue-600/10 border border-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white border border-transparent' }}">
                        <svg class="w-5 h-5 transition group-hover:scale-105" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Admin
                    </a>
                </nav>

                <!-- Sidebar Footer User Panel -->
                <div class="p-4 border-t border-slate-800/60 bg-slate-950/60 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-blue-600 to-purple-600 flex items-center justify-center text-sm font-bold text-white shadow-md">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-slate-400 capitalize truncate">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                    
                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0 shrink-0">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors" title="Logout">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- ====== MAIN CONTENT PANEL ====== -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                
                <!-- ====== TOP NAVBAR ====== -->
                <header class="h-16 border-b border-slate-800/60 backdrop-blur-md bg-slate-950/60 flex items-center justify-between px-6 shrink-0 relative z-20">
                    
                    <!-- Left: Mobile Sidebar Toggle and Page title -->
                    <div class="flex items-center gap-4">
                        <button class="md:hidden text-slate-400 hover:text-white" @click="mobileSidebarOpen = true">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="hidden sm:flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs text-slate-400 font-semibold tracking-wide uppercase">Sistem OCR Telegram Aktif</span>
                        </div>
                    </div>

                    <!-- Right: Quick Settings & User Info -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Webhook status indicator -->
                        <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-xl">
                            <span class="text-[10px]">WEBHOOK BOT:</span>
                            @if(env('TELEGRAM_WEBHOOK_URL') || env('APP_URL') !== 'http://localhost')
                                <span class="text-emerald-400 font-bold">AKTIF</span>
                            @else
                                <span class="text-amber-400 font-bold">BELUM DIATUR</span>
                            @endif
                        </div>

                        <!-- Theme Toggle Button -->
                        <button id="theme-toggle" type="button" class="w-9 h-9 rounded-xl border border-slate-800/80 bg-slate-900/60 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-all relative group" title="Ubah Tema">
                            <!-- Sun icon for light theme -->
                            <svg id="theme-toggle-light-icon" class="w-4.5 h-4.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <!-- Moon icon for dark theme -->
                            <svg id="theme-toggle-dark-icon" class="w-4.5 h-4.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Date Time Display -->
                        <div class="text-xs text-slate-400 font-semibold hidden md:block">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </header>

                <!-- ====== FLASH MESSAGES ====== -->
                @if(session('success') || session('error') || session('warning') || session('info'))
                <div class="px-6 pt-4 m-0 relative z-10">
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between p-4 mb-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-400 hover:text-emerald-200">&times;</button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between p-4 mb-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-200">&times;</button>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between p-4 mb-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span>{{ session('warning') }}</span>
                        </div>
                        <button @click="show = false" class="text-amber-400 hover:text-amber-200">&times;</button>
                    </div>
                    @endif
                </div>
                @endif

                <!-- ====== VIEW SLOT CONTENT ====== -->
                <main class="flex-1 p-6 relative z-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')

        <!-- Theme Toggle Event Handler -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var themeToggleBtn = document.getElementById('theme-toggle');
                var darkIcon = document.getElementById('theme-toggle-dark-icon');
                var lightIcon = document.getElementById('theme-toggle-light-icon');

                function updateToggleIcons() {
                    if (document.documentElement.classList.contains('dark')) {
                        lightIcon.classList.remove('hidden');
                        darkIcon.classList.add('hidden');
                    } else {
                        darkIcon.classList.remove('hidden');
                        lightIcon.classList.add('hidden');
                    }
                }

                updateToggleIcons();

                if (themeToggleBtn) {
                    themeToggleBtn.addEventListener('click', function() {
                        var newTheme = 'dark';
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            document.documentElement.classList.add('light');
                            newTheme = 'light';
                            localStorage.setItem('theme', 'light');
                        } else {
                            document.documentElement.classList.remove('light');
                            document.documentElement.classList.add('dark');
                            newTheme = 'dark';
                            localStorage.setItem('theme', 'dark');
                        }
                        updateToggleIcons();
                        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
                    });
                }
            });
        </script>
    </body>
</html>
