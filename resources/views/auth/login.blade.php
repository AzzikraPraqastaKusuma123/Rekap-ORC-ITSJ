<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="m-0 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block mt-1 w-full text-sm font-semibold" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="password" class="block mt-1 w-full text-sm font-semibold"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900 focus:outline-none shadow-sm cursor-pointer" name="remember">
                <span class="ms-2.5 text-xs text-slate-400 select-none hover:text-slate-300 transition">Ingat sesi saya</span>
            </label>
        </div>

        <!-- Links Row & Submission -->
        <div class="flex flex-col gap-3.5 mt-6 pt-4 border-t border-slate-800/50">
            <div class="flex items-center justify-between text-xs">
                @if (Route::has('password.request'))
                    <a class="text-slate-400 hover:text-white transition-colors" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
                
                <a class="text-blue-400 hover:text-blue-300 transition-colors font-bold" href="{{ route('register') }}">
                    Daftar Akun Baru
                </a>
            </div>

            <div class="flex justify-end mt-2">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                    Masuk Dasbor
                </button>
            </div>
        </div>
    </form>
</x-guest-layout>
