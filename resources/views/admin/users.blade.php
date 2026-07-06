<x-app-layout>
    {{-- ====== PAGE HEADER ====== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight font-display text-slate-900 dark:text-white">
                Manajemen Karyawan
            </h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola data login staf operasional dan struktur direktur.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" @click="$dispatch('open-add-user-modal')"
                class="btn-primary py-2 px-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    {{-- ====== USERS TABLE ====== --}}
    <div class="pro-card p-0 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 font-bold">
                    <tr>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">Nama</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">Email</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">Kewenangan (Role)</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">Tanggal Bergabung</th>
                        <th class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50"
                    x-data="{ openRoleModal: false, userId: null, currentRole: '' }">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === 'superadmin')
                                    <span
                                        class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">Super
                                        Administrator</span>
                                @elseif($user->role === 'admin')
                                    <span
                                        class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">Direktur
                                        Keuangan</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Staf
                                        Operasional</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button
                                    @click="userId = {{ $user->id }}; currentRole = '{{ $user->role }}'; openRoleModal = true"
                                    class="text-xs px-2 py-1 rounded bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-blue-900/30 transition-colors">
                                    Edit Role
                                </button>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                        class="m-0 inline-block"
                                        onsubmit="return confirm('Hapus permanen akun karyawan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs px-2 py-1 rounded bg-slate-100 text-red-500 hover:bg-red-50 dark:bg-slate-800 dark:hover:bg-red-900/30 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- Role Edit Modal --}}
                    <div x-show="openRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openRoleModal = false">
                        </div>
                        <div class="relative bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6 shadow-xl"
                            @click.stop>
                            <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white mb-2">Ubah
                                Kewenangan Karyawan</h3>
                            <form :action="`/admin/users/${userId}/role`" method="POST">
                                @csrf
                                <div class="mb-5">
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Role
                                        Akses</label>
                                    <select name="role" x-model="currentRole" class="form-input text-sm">
                                        <option value="superadmin">Super Administrator (Penuh)</option>
                                        <option value="admin">Direktur Keuangan (Approval)</option>
                                        <option value="staff">Staf Lapangan (Input Data)</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 justify-end mt-6">
                                    <button type="button" @click="openRoleModal = false"
                                        class="btn-secondary px-4 py-2">Batal</button>
                                    <button type="submit" class="btn-primary px-4 py-2">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ====== ADD USER MODAL ====== --}}
    <div x-data="{ open: false }" @open-add-user-modal.window="open = true">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="open = false"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6 shadow-xl" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Pendaftaran Karyawan Baru
                    </h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama
                            Lengkap</label>
                        <input type="text" name="name" required class="form-input text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat
                            Email</label>
                        <input type="email" name="email" required class="form-input text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kata
                            Sandi</label>
                        <input type="password" name="password" required class="form-input text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tetapkan
                            Kewenangan (Role)</label>
                        <select name="role" required class="form-input text-sm">
                            <option value="staff">Staf Lapangan (Input Data)</option>
                            <option value="admin">Direktur Keuangan (Approval)</option>
                            <option value="superadmin">Super Administrator (Penuh)</option>
                        </select>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="btn-primary w-full justify-center py-2.5">Daftarkan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>