<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-400 tracking-wide">
            Hapus Akun
        </h2>

        <p class="mt-1.5 text-xs text-slate-400 leading-relaxed">
            Tindakan ini tidak dapat dibatalkan. Setelah akun Anda dihapus, semua data profil, pengaturan credential, dan riwayat rekap struk belanja akan dihapus secara permanen dari server. Pastikan Anda telah mengunduh riwayat laporan keuangan Anda sebelum melanjutkan.
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-500 transition shadow-lg shadow-red-500/10">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 md:p-8 bg-slate-900 border border-slate-800 text-slate-200 rounded-3xl m-0 space-y-6">
            @csrf
            @method('delete')

            <div>
                <h3 class="text-lg font-extrabold text-white tracking-wide">
                    Apakah Anda yakin ingin menghapus akun ini secara permanen?
                </h3>

                <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                    Setelah konfirmasi selesai, seluruh data dan catatan transaksi struk Anda akan dihapus secara total dari sistem tanpa kemungkinan dipulihkan. Silakan masukkan kata sandi Anda untuk memverifikasi tindakan kritis ini.
                </p>
            </div>

            <div>
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full text-sm font-semibold"
                    placeholder="Masukkan Kata Sandi Konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3.5 pt-4 border-t border-slate-800/60">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 bg-slate-800 hover:bg-slate-700 transition">
                    Batal
                </button>

                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-500 transition shadow">
                    Hapus Akun Secara Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>
