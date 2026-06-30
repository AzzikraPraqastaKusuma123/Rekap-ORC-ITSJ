<x-guest-layout>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold font-display dark:text-white text-slate-900">Verifikasi Email Anda</h2>
        <p class="text-sm text-slate-400 mt-1">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan. Jika tidak menerima email, kami dapat mengirimkan ulang.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3.5 rounded-xl text-sm font-semibold text-emerald-400"
             style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat mendaftar.
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="m-0">
            @csrf
            <button type="submit" class="btn-primary w-full justify-center py-2.5 text-xs">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <div class="h-px bg-slate-800/40" style="background:rgba(255,255,255,0.06);"></div>

        <form method="POST" action="{{ route('logout') }}" class="m-0 flex justify-center">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-400 hover:text-white transition-colors py-2">
                Keluar Aplikasi
            </button>
        </form>
    </div>
</x-guest-layout>
