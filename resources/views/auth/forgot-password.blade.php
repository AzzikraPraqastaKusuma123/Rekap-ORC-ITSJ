<x-guest-layout>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold font-display dark:text-white text-slate-900">Lupa Kata Sandi?</h2>
        <p class="text-sm text-slate-400 mt-1">Cukup masukkan alamat email terdaftar Anda di bawah ini, dan kami akan mengirimkan tautan pemulihan untuk menyetel ulang kata sandi baru.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="m-0 space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1.5">
            <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 font-display">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" 
                       value="{{ old('email') }}" 
                       required autofocus
                       class="block w-full pl-10 pr-4 py-3 rounded-xl text-sm font-medium {{ $errors->has('email') ? 'border-red-500/50' : '' }}"
                       placeholder="nama@perusahaan.com">
            </div>
            @error('email')
            <p class="text-xs font-semibold text-red-400 flex items-center gap-1.5 mt-1">
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Divider -->
        <div class="h-px bg-slate-800/40" style="background:rgba(255,255,255,0.06);"></div>

        <div class="flex items-center justify-between mt-6 pt-4">
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="{{ route('login') }}">
                Kembali ke Login
            </a>

            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                Kirim Tautan Pemulihan
            </button>
        </div>
    </form>
</x-guest-layout>
