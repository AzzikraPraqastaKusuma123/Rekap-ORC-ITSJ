<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="m-0 space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="name" class="block mt-1 w-full text-sm font-semibold" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block mt-1 w-full text-sm font-semibold" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi Baru" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="password" class="block mt-1 w-full text-sm font-semibold"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm font-semibold"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-800/50">
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="{{ route('login') }}">
                Sudah terdaftar? Masuk
            </a>

            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                Daftar Akun
            </button>
        </div>
    </form>
</x-guest-layout>
