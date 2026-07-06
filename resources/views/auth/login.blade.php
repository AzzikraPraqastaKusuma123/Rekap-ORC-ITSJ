<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-5 p-3.5 rounded-xl text-sm font-semibold text-emerald-400"
            style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-bold font-display dark:text-white text-slate-900">Selamat Datang</h2>
            <p class="text-sm text-slate-400 mt-1">Masuk untuk mengakses dasbor rekap keuangan Anda.</p>
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
            <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 font-display">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="block w-full pl-10 pr-4 py-3 rounded-xl text-sm font-medium {{ $errors->has('email') ? 'border-red-500/50' : '' }}"
                    placeholder="nama@perusahaan.com">
            </div>
            @error('email')
                <p class="text-xs font-semibold text-red-400 flex items-center gap-1.5 mt-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password"
                    class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 font-display">
                    Kata Sandi
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-[11px] font-semibold text-blue-400 hover:text-blue-300 transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="relative" x-data="{ showPass: false }">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"
                    class="block w-full pl-10 pr-11 py-3 rounded-xl text-sm font-medium {{ $errors->has('password') ? 'border-red-500/50' : '' }}"
                    placeholder="••••••••">
                <button type="button" @click="showPass = !showPass"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-xs font-semibold text-red-400 flex items-center gap-1.5 mt-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div>
            <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded cursor-pointer"
                    style="accent-color:#3b82f6;">
                <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors select-none">
                    Ingat saya di perangkat ini
                </span>
            </label>
        </div>

        <!-- Divider -->
        <div class="h-px" style="background:rgba(255,255,255,0.06);"></div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <button type="submit" class="btn-primary w-full justify-center py-3 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Masuk ke Dasbor
            </button>

            <!-- Registration link removed for Enterprise environment (Invite Only) -->
        </div>
    </form>
</x-guest-layout>