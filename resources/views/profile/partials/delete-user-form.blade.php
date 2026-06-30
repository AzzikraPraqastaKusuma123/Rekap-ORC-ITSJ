<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-600 dark:text-red-400 tracking-wide">
            Hapus Akun
        </h2>

        <p class="mt-1.5 text-sm text-slate-500 leading-relaxed">
            Tindakan ini tidak dapat dibatalkan. Setelah akun Anda dihapus, semua data profil, pengaturan credential, dan riwayat rekap struk belanja akan dihapus secara permanen dari server. Pastikan Anda telah mengunduh riwayat laporan keuangan Anda sebelum melanjutkan.
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-500 transition shadow-sm">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 md:p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-200 rounded-2xl m-0 space-y-6">
            @csrf
            @method('delete')

            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                    Apakah Anda yakin ingin menghapus akun ini secara permanen?
                </h3>

                <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                    Setelah konfirmasi selesai, seluruh data dan catatan transaksi struk Anda akan dihapus secara total dari sistem tanpa kemungkinan dipulihkan. Silakan masukkan kata sandi Anda untuk memverifikasi tindakan kritis ini.
                </p>
            </div>

            <div>
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="form-input block w-full text-sm px-4 py-2.5"
                    placeholder="Masukkan Kata Sandi Konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 rounded-lg text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 dark:text-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 transition">
                    Batal
                </button>

                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-500 transition shadow-sm">
                    Hapus Akun Secara Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>
