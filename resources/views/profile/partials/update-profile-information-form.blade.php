<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-white tracking-wide">
            Informasi Profil
        </h2>

        <p class="mt-1.5 text-xs text-slate-400 leading-relaxed">
            Perbarui data diri profil pengguna dan alamat email terdaftar pada akun dasbor Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-sm font-semibold" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-sm font-semibold" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3.5 rounded-xl bg-slate-950/40 border border-slate-800/60">
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Alamat email Anda belum melalui tahap verifikasi.

                        <button form="send-verification" class="underline font-bold text-blue-400 hover:text-blue-300 transition focus:outline-none">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-emerald-400">
                            Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-emerald-400"
                >Berhasil disimpan.</span>
            @endif
        </div>
    </form>
</section>
