<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">
            Perbarui Kata Sandi
        </h2>

        <p class="mt-1.5 text-sm text-slate-500 leading-relaxed">
            Pastikan akun dasbor Anda menggunakan kredensial kata sandi yang kuat dan unik untuk menjaga keamanan data finansial Anda.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata Sandi Sekarang" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="form-input block w-full text-sm px-4 py-2.5" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata Sandi Baru" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" class="form-input block w-full text-sm px-4 py-2.5" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-input block w-full text-sm px-4 py-2.5" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                Perbarui Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-bold text-emerald-600 dark:text-emerald-400"
                >Kata sandi berhasil diperbarui.</span>
            @endif
        </div>
    </form>
</section>
