<x-guest-layout>
    <div class="mb-6 text-xs text-slate-400 leading-relaxed">
        Masukkan alamat email Anda dan atur kata sandi baru untuk memulihkan akses dasbor.
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="m-0 space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block mt-1 w-full text-sm font-semibold" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi Baru" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="password" class="block mt-1 w-full text-sm font-semibold" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm font-semibold" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-800/50">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow">
                Setel Ulang Kata Sandi
            </button>
        </div>
    </form>
</x-guest-layout>
