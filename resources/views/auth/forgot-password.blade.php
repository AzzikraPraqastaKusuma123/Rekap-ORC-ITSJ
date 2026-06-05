<x-guest-layout>
    <div class="mb-6 text-xs text-slate-400 leading-relaxed">
        Lupa kata sandi akun Anda? Cukup masukkan alamat email terdaftar Anda di bawah ini, dan kami akan mengirimkan tautan pemulihan untuk menyetel ulang kata sandi baru.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="m-0 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block mt-1 w-full text-sm font-semibold" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-800/50">
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="{{ route('login') }}">
                Kembali ke Login
            </a>

            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow">
                Kirim Tautan Pemulihan
            </button>
        </div>
    </form>
</x-guest-layout>
