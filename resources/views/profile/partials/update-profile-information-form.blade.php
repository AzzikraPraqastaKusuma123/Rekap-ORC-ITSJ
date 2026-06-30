<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">
            Informasi Profil
        </h2>

        <p class="mt-1.5 text-sm text-slate-500 leading-relaxed">
            Perbarui data diri profil pengguna dan alamat email terdaftar pada akun dasbor Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
            <x-text-input id="name" name="name" type="text" class="form-input block w-full text-sm px-4 py-2.5" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
            <x-text-input id="email" name="email" type="email" class="form-input block w-full text-sm px-4 py-2.5" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                    <p class="text-sm text-amber-800 dark:text-amber-400 leading-relaxed">
                        Alamat email Anda belum melalui tahap verifikasi.

                        <button form="send-verification" class="underline font-bold hover:text-amber-600 dark:hover:text-amber-300 transition focus:outline-none">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                            Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-bold text-emerald-600 dark:text-emerald-400"
                >Berhasil disimpan.</span>
            @endif
        </div>
    </form>
</section>
