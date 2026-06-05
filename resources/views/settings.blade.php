<x-app-layout>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Pengaturan Sistem</h1>
        <p class="text-sm text-slate-400 mt-1">Konfigurasikan kredensial Bot Telegram, direktori lokal Tesseract OCR, serta integrasi ID akun pengguna.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- COLUMN 1 & 2: Main Configurations -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card: Environment & API Credentials -->
            <div class="glass-card rounded-2xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0 border border-blue-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-wide">Kredensial OCR & API</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Kelola direktori executable Tesseract OCR lokal dan token API Bot Telegram resmi Anda.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.update') }}" class="m-0 space-y-6">
                    @csrf
                    
                    <!-- Telegram Bot Token -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Token API Bot Telegram</label>
                        <div class="relative">
                            <input type="password" name="telegram_bot_token" value="{{ env('TELEGRAM_BOT_TOKEN') === 'YOUR_BOT_TOKEN_HERE' ? '' : env('TELEGRAM_BOT_TOKEN') }}" placeholder="Contoh: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ"
                                   class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white placeholder-slate-600 focus:border-blue-500 focus:outline-none transition font-mono">
                        </div>
                        <span class="block text-[10px] text-slate-500 mt-1.5">Dapatkan token API resmi ini dari layanan <a href="https://t.me/BotFather" target="_blank" class="text-blue-400 hover:underline">@BotFather</a> di aplikasi Telegram.</span>
                    </div>

                    <!-- Tesseract Path -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Direktori Executable Tesseract OCR</label>
                        <input type="text" name="tesseract_path" value="{{ env('TESSERACT_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe') }}" placeholder="Contoh: C:\Program Files\Tesseract-OCR\tesseract.exe"
                               class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition font-mono">
                        <div class="mt-2 p-3.5 rounded-xl bg-slate-950/40 border border-slate-800/60 text-xs text-slate-400 leading-relaxed">
                            💡 <b>Catatan Deteksi Sistem:</b> Tesseract OCR terdeteksi terpasang pada direktori Windows berikut:<br>
                            <code class="text-cyan-400 select-all font-semibold font-mono block mt-1.5">C:\Program Files\Tesseract-OCR\tesseract.exe</code>
                        </div>
                    </div>

                    <!-- Save credentials trigger -->
                    <div class="pt-4 border-t border-slate-800/60 flex justify-end">
                        <button type="submit" class="px-6 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                            Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card: Webhook registration tools -->
            <div class="glass-card rounded-2xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-wide">Registrasi Webhook API</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftarkan endpoint server Anda ke dalam sistem perutean pesan otomatis Telegram.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-sm text-slate-300 leading-relaxed mb-3">
                            Agar Telegram dapat meneruskan foto struk secara otomatis ke server Laravel Anda secara instan, tautkan URL webhook berikut ke server API Telegram:
                        </p>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 text-xs font-mono select-all text-slate-300">
                            {{ rtrim(env('APP_URL'), '/') }}/api/telegram/webhook
                        </div>
                    </div>

                    <!-- Webhook Local IP Warnings -->
                    @if(str_contains(env('APP_URL'), 'localhost') || str_contains(env('APP_URL'), '127.0.0.1'))
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-400 leading-relaxed flex items-start gap-3">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <div>
                                <b class="font-extrabold block mb-1">Alamat Lokal Terdeteksi ({{ env('APP_URL') }})</b>
                                API Telegram memerlukan alamat publik HTTPS untuk pengiriman webhook. Solusi pengerjaan:
                                <ul class="list-disc pl-4 mt-1 space-y-1">
                                    <li>Gunakan utilitas tunneling seperti <b>ngrok</b> atau <b>Localtunnel</b> untuk mengekspos port lokal Anda.</li>
                                    <li>Perbarui variabel <code class="bg-slate-950/80 px-1 py-0.5 rounded">APP_URL</code> pada file `.env` dengan alamat HTTPS publik baru Anda.</li>
                                    <li>Muat ulang halaman ini, lalu klik tombol registrasi webhook di bawah.</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.webhook') }}" class="m-0 flex items-center gap-4">
                        @csrf
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-tr from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 transition shadow-lg shadow-emerald-500/10 flex items-center gap-2">
                            Daftarkan Webhook Otomatis
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- COLUMN 3: User Profile Linking Instructions -->
        <div class="space-y-6">
            
            <!-- Card: Telegram Account Integration -->
            <div class="glass-card rounded-2xl p-6 flex flex-col">
                <div class="flex items-center gap-3 mb-6 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 shrink-0 border border-purple-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-wide">Tautan Akun Telegram</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Hubungkan ID akun Telegram untuk sinkronisasi pengiriman struk belanja.</p>
                    </div>
                </div>

                <div class="flex-1 flex flex-col justify-between space-y-6">
                    
                    <!-- Steps Checklist -->
                    <div class="space-y-4">
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Ikuti langkah mudah berikut untuk mengintegrasikan akun Telegram Anda dengan dasbor admin:
                        </p>

                        <div class="space-y-3.5 text-xs text-slate-400 leading-relaxed">
                            <div class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-950 text-slate-300 border border-slate-800 flex items-center justify-center font-bold shrink-0">1</span>
                                <div>
                                    Cari username bot Anda di Telegram, atau klik langsung tautan di bawah:
                                    @if(env('TELEGRAM_BOT_TOKEN') && env('TELEGRAM_BOT_TOKEN') !== 'YOUR_BOT_TOKEN_HERE')
                                        <a href="https://t.me/{{ explode(':', env('TELEGRAM_BOT_TOKEN'))[0] ?? 'Bot' }}" target="_blank" class="text-blue-400 font-bold hover:underline block mt-1">Buka Chat Bot ↗</a>
                                    @else
                                        <span class="text-slate-500 italic block mt-1">Konfigurasikan token bot terlebih dahulu</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-950 text-slate-300 border border-slate-800 flex items-center justify-center font-bold shrink-0">2</span>
                                <span>Kirimkan perintah <code class="bg-slate-950 px-1 py-0.5 rounded font-mono text-cyan-400">/start</code> ke ruang obrolan bot tersebut.</span>
                            </div>
                            <div class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-950 text-slate-300 border border-slate-800 flex items-center justify-center font-bold shrink-0">3</span>
                                <span>Bot akan otomatis membalas dan menampilkan nomor unik <b>Telegram Chat ID</b> Anda.</span>
                            </div>
                            <div class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-950 text-slate-300 border border-slate-800 flex items-center justify-center font-bold shrink-0">4</span>
                                <span>Salin nomor tersebut, tempel pada kolom input di bawah ini, lalu simpan!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input form to save Chat ID -->
                    <div class="pt-4 border-t border-slate-800/60 shrink-0">
                        <form method="POST" action="{{ route('settings.update') }}" class="m-0 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">ID Chat Telegram Anda</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="telegram_chat_id" value="{{ $user->telegram_chat_id }}" placeholder="Contoh: 987654321" required
                                           class="flex-1 px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition font-mono">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow">
                                        Hubungkan
                                    </button>
                                </div>
                                
                                <div class="mt-3.5 flex items-center justify-between text-[10px] font-semibold">
                                    <span class="text-slate-500 uppercase tracking-wider">Status Koneksi Akun:</span>
                                    @if(!empty($user->telegram_chat_id))
                                        <span class="text-emerald-400 font-bold uppercase tracking-wider bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/10">TERHUBUNG</span>
                                    @else
                                        <span class="text-rose-400 font-bold uppercase tracking-wider bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/10">BELUM TERHUBUNG</span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </div>

</x-app-layout>
