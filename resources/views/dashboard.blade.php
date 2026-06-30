<x-app-layout>
    @push('head')
        <!-- Load ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush

    <!-- Header Greeting Section -->
    <div class="mb-5 md:mb-8">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight font-display text-slate-900 dark:text-white">
                    Ikhtisar Keuangan</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Laporan terpadu hasil ekstraksi data struk belanja
                    digital secara instan.</p>
            </div>
            <!-- Desktop buttons -->
            <div class="hidden md:flex items-center gap-3 flex-shrink-0">
                <button type="button"
                    @click="$dispatch('open-upload-modal'); setTimeout(() => { $dispatch('set-upload-mode', 'upload') }, 50)"
                    class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Upload File
                </button>
                <button type="button"
                    @click="$dispatch('open-upload-modal'); setTimeout(() => { $dispatch('set-upload-mode', 'camera') }, 50)"
                    class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                    Buka Kamera
                </button>
            </div>
        </div>
        <!-- Mobile: Full-width CTA buttons -->
        <div class="flex md:hidden gap-2">
            <button type="button"
                @click="$dispatch('open-upload-modal'); setTimeout(() => { $dispatch('set-upload-mode', 'upload') }, 50)"
                class="btn-secondary w-full justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Upload File
            </button>
            <button type="button"
                @click="$dispatch('open-upload-modal'); setTimeout(() => { $dispatch('set-upload-mode', 'camera') }, 50)"
                class="btn-primary w-full justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
                Kamera
            </button>
        </div>
    </div>

    @include('receipts.partials.upload-modal')

    <!-- ====== 1. STATISTIC CARDS ROW ====== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-5 md:mb-8 stat-grid-mobile">

        <!-- Card: Spent Today -->
        <div class="pro-card p-4 md:p-6 border-t-4 border-t-blue-500">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <span class="text-[10px] md:text-xs font-bold text-slate-500 tracking-wider uppercase font-display">Hari
                    Ini</span>
                <div
                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight font-display">Rp
                    {{ number_format($stats['today']['total'], 0, ',', '.') }}
                </h3>
                <div class="flex items-center gap-1 mt-1.5">
                    @if($stats['today']['change_percentage'] > 0)
                        <span class="text-[10px] md:text-xs font-semibold text-red-500">▲
                            {{ abs($stats['today']['change_percentage']) }}%</span>
                    @elseif($stats['today']['change_percentage'] < 0)
                        <span class="text-[10px] md:text-xs font-semibold text-green-500">▼
                            {{ abs($stats['today']['change_percentage']) }}%</span>
                    @else
                        <span class="text-[10px] md:text-xs font-semibold text-slate-500">0%</span>
                    @endif
                    <span
                        class="text-[8px] md:text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs
                        kemarin</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Week -->
        <div class="pro-card p-4 md:p-6 border-t-4 border-t-purple-500">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <span
                    class="text-[10px] md:text-xs font-bold text-slate-500 tracking-wider uppercase font-display">Minggu
                    Ini</span>
                <div
                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight font-display">Rp
                    {{ number_format($stats['week']['total'], 0, ',', '.') }}
                </h3>
                <div class="flex items-center gap-1 mt-1.5">
                    @if($stats['week']['change_percentage'] > 0)
                        <span class="text-[10px] md:text-xs font-semibold text-red-500">▲
                            {{ abs($stats['week']['change_percentage']) }}%</span>
                    @elseif($stats['week']['change_percentage'] < 0)
                        <span class="text-[10px] md:text-xs font-semibold text-green-500">▼
                            {{ abs($stats['week']['change_percentage']) }}%</span>
                    @else
                        <span class="text-[10px] md:text-xs font-semibold text-slate-500">0%</span>
                    @endif
                    <span
                        class="text-[8px] md:text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs
                        pekan lalu</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Month -->
        <div class="pro-card p-4 md:p-6 border-t-4 border-t-teal-500">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <span
                    class="text-[10px] md:text-xs font-bold text-slate-500 tracking-wider uppercase font-display">Bulan
                    Ini</span>
                <div
                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-teal-100 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight font-display">Rp
                    {{ number_format($stats['month']['total'], 0, ',', '.') }}
                </h3>
                <div class="flex items-center gap-1 mt-1.5">
                    @if($stats['month']['change_percentage'] > 0)
                        <span class="text-[10px] md:text-xs font-semibold text-red-500">▲
                            {{ abs($stats['month']['change_percentage']) }}%</span>
                    @elseif($stats['month']['change_percentage'] < 0)
                        <span class="text-[10px] md:text-xs font-semibold text-green-500">▼
                            {{ abs($stats['month']['change_percentage']) }}%</span>
                    @else
                        <span class="text-[10px] md:text-xs font-semibold text-slate-500">0%</span>
                    @endif
                    <span
                        class="text-[8px] md:text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs
                        bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Year -->
        <div class="pro-card p-4 md:p-6 border-t-4 border-t-emerald-500">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <span
                    class="text-[10px] md:text-xs font-bold text-slate-500 tracking-wider uppercase font-display">Tahun
                    Ini</span>
                <div
                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.252.58 1.802l-3.97 2.887a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.887a1 1 0 00-1.176 0l-3.97 2.887c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.97-2.887c-.78-.55-.38-1.81.58-1.802h4.907a1 1 0 00.95-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight font-display">Rp
                    {{ number_format($stats['year']['total'], 0, ',', '.') }}
                </h3>
                <div class="flex items-center gap-1 mt-1.5">
                    @if($stats['year']['change_percentage'] > 0)
                        <span class="text-[10px] md:text-xs font-semibold text-red-500">▲
                            {{ abs($stats['year']['change_percentage']) }}%</span>
                    @elseif($stats['year']['change_percentage'] < 0)
                        <span class="text-[10px] md:text-xs font-semibold text-green-500">▼
                            {{ abs($stats['year']['change_percentage']) }}%</span>
                    @else
                        <span class="text-[10px] md:text-xs font-semibold text-slate-500">0%</span>
                    @endif
                    <span
                        class="text-[8px] md:text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs
                        tahun lalu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== 2. CHARTS DISPLAY ROW ====== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-5 md:mb-8">

        <!-- Spending Trend Area Chart -->
        <div class="lg:col-span-2 pro-card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight font-display">Grafik
                        Fluktuasi Harian</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Analisis akumulasi transaksi belanja harian Anda selama 15
                        hari terakhir.</p>
                </div>
                <span class="badge badge-info">REALTIME</span>
            </div>

            <div id="trend-chart" class="w-full h-80"></div>
        </div>

        <!-- Store Shares Donut Chart -->
        <div class="pro-card p-6 flex flex-col">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight font-display">Distribusi
                    Merchant</h3>
                <p class="text-xs text-slate-500 mt-0.5">Peta pembagian anggaran pengeluaran berdasarkan merchant atau
                    toko ritel.</p>
            </div>

            <div class="flex-1 flex items-center justify-center">
                <div id="store-chart" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- ====== 3. RECENT TRANSACTIONS & LOG TIMELINES ====== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

        <!-- Recent Transactions Table -->
        <div class="lg:col-span-2 pro-card p-6 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight font-display">Riwayat
                        Transaksi Terbaru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Struk belanja terbaru yang diekstraksi dan diverifikasi
                        oleh sistem.</p>
                </div>
                <a href="{{ route('receipts.index') }}"
                    class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 font-display">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Desktop View: Table Layout -->
            <div class="hidden md:block overflow-x-auto flex-1">
                <table class="w-full text-sm text-left data-table">
                    <thead>
                        <tr>
                            <th class="pb-3 pl-2">Foto</th>
                            <th class="pb-3">Kode Transaksi</th>
                            <th class="pb-3">Merchant</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right pr-2">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody id="recent-transactions-desktop"
                        class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($recentTransactions as $tx)
                            @php
                                $storeInitial = strtoupper(substr($tx->store_name ?? 'S', 0, 1));
                                $charVal = ord($storeInitial);
                                $colors = [
                                    'bg-blue-600',
                                    'bg-emerald-600',
                                    'bg-amber-500',
                                    'bg-red-500',
                                    'bg-purple-600',
                                    'bg-pink-600',
                                    'bg-cyan-600',
                                    'bg-orange-500'
                                ];
                                $colorClass = $colors[$charVal % count($colors)];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-2 pl-2">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 cursor-pointer border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
                                        onclick="window.location.href='{{ route('receipts.index', ['edit' => $tx->id]) }}'"
                                        title="Klik untuk Detail / Koreksi">
                                        @if($tx->receipt_image)
                                            <img src="{{ asset('storage/' . $tx->receipt_image) }}" alt="Struk"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center font-bold text-white text-sm {{ $colorClass }}">
                                                {{ $storeInitial }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 font-mono text-xs text-slate-500 dark:text-slate-400">
                                    <a href="{{ route('receipts.index', ['edit' => $tx->id]) }}"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                        {{ $tx->receipt_code }}
                                    </a>
                                </td>
                                <td class="py-3.5 font-bold text-slate-900 dark:text-white font-display text-sm">
                                    <a href="{{ route('receipts.index', ['edit' => $tx->id]) }}"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                        {{ $tx->store_name }}
                                    </a>
                                </td>
                                <td class="py-3.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ Carbon\Carbon::parse($tx->receipt_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 text-xs">
                                    @if($tx->total_price == 0)
                                        <span class="badge badge-warning flex items-center gap-1.5 max-w-fit">
                                            Koreksi
                                        </span>
                                    @else
                                        <span class="badge badge-success flex items-center gap-1.5 max-w-fit">
                                            Verif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-right font-bold text-slate-900 dark:text-white pr-2 font-display">
                                    Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 font-medium">
                                    Belum ada catatan pengeluaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Modern Financial Feed List Layout -->
            <div id="recent-transactions-mobile" class="block md:hidden flex-1">
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentTransactions as $tx)
                        @php
                            $storeInitial = strtoupper(substr($tx->store_name ?? 'S', 0, 1));
                            $charVal = ord($storeInitial);
                            $colors = [
                                'bg-blue-600',
                                'bg-emerald-600',
                                'bg-amber-500',
                                'bg-red-500',
                                'bg-purple-600',
                                'bg-pink-600',
                                'bg-cyan-600',
                                'bg-orange-500'
                            ];
                            $colorClass = $colors[$charVal % count($colors)];
                        @endphp
                        <div class="flex items-center justify-between py-3 px-1 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition duration-200 cursor-pointer"
                            onclick="window.location.href='{{ route('receipts.index', ['edit' => $tx->id]) }}'">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                    @if($tx->receipt_image)
                                        <img src="{{ asset('storage/' . $tx->receipt_image) }}" alt="Struk"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center font-bold text-white text-sm {{ $colorClass }}">
                                            {{ $storeInitial }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display truncate">
                                        {{ $tx->store_name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-slate-500">
                                            {{ Carbon\Carbon::parse($tx->receipt_date)->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-[10px] font-bold">
                                            @if($tx->total_price == 0)
                                                <span class="text-amber-500">Koreksi</span>
                                            @else
                                                <span class="text-emerald-500">Verif</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-bold text-slate-900 dark:text-white font-display">
                                    Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                                </span>
                                <div class="text-[9px] font-mono text-slate-500 mt-0.5">
                                    {{ $tx->receipt_code }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 font-medium text-xs">
                            Belum ada catatan pengeluaran.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Products Bar Chart -->
        <div class="pro-card p-6 flex flex-col">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight font-display">Barang Paling
                    Sering Dibeli</h3>
                <p class="text-xs text-slate-500 mt-0.5">Visualisasi frekuensi pembelian barang.</p>
            </div>

            <div class="flex-1 flex flex-col justify-between">
                <div id="product-chart" class="w-full"></div>

                <!-- Live Logs / Activity timeline header -->
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <h4
                        class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 font-display">
                        Log Aktivitas</h4>
                    <div id="activity-logs-container" class="space-y-3.5">
                        @forelse($activityLogs as $log)
                            <div class="flex gap-2 text-xs">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mt-1 shrink-0"></span>
                                <div class="flex flex-col overflow-hidden">
                                    <span
                                        class="font-bold text-slate-700 dark:text-slate-300 truncate">{{ $log->activity }}</span>
                                    <span
                                        class="text-[10px] text-slate-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <span class="text-xs text-slate-500 font-medium">Belum ada aktivitas tercatat.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Detect active theme instantly
            var initialTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            var isMobile = window.innerWidth < 768;

            // Simple Chart rendering
            var chartOptions = {
                chart: {
                    background: 'transparent',
                    animations: { enabled: false }, // disable animations completely for speed
                    toolbar: { show: false }
                },
                theme: { mode: initialTheme },
                dataLabels: { enabled: false }
            };

            // 1. Daily Expenditure Trend Area Chart
            var trendOptions = {
                ...chartOptions,
                chart: { type: 'area', height: 320, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: false } },
                stroke: { curve: 'smooth', width: 3, colors: ['#2563eb'] },
                fill: { type: 'solid', colors: ['#2563eb'], opacity: 0.1 },
                series: [{ name: 'Total Belanja (Rp)', data: {!! json_encode($spendingTrend['series']) !!} }],
                xaxis: {
                    categories: {!! json_encode($spendingTrend['labels']) !!},
                    labels: { style: { colors: initialTheme === 'light' ? '#64748b' : '#94a3b8', fontSize: '10px' } }
                },
                yaxis: {
                    labels: {
                        style: { colors: initialTheme === 'light' ? '#64748b' : '#94a3b8', fontSize: '10px' },
                        formatter: function (val) { return val.toLocaleString('id-ID'); }
                    }
                },
                tooltip: {
                    y: { formatter: function (val) { return "Rp " + val.toLocaleString('id-ID'); } }
                },
                grid: { borderColor: initialTheme === 'light' ? '#e2e8f0' : '#334155' }
            };
            var trendChart = new ApexCharts(document.querySelector("#trend-chart"), trendOptions);
            trendChart.render();

            // 2. Store Spending Shares Donut Chart
            var storeOptions = {
                ...chartOptions,
                chart: { type: 'donut', height: 280, animations: { enabled: false } },
                labels: {!! json_encode($storeSpending['labels']) !!},
                series: {!! json_encode($storeSpending['series']) !!},
                colors: ['#2563eb', '#0d9488', '#e11d48', '#d97706', '#7c3aed', '#0284c7'],
                stroke: { show: false },
                tooltip: {
                    y: { formatter: function (val) { return "Rp " + val.toLocaleString('id-ID'); } }
                },
                legend: { position: 'bottom', fontSize: '11px', labels: { colors: initialTheme === 'light' ? '#64748b' : '#94a3b8' } },
                plotOptions: { pie: { donut: { labels: { show: false } } } }
            };
            var storeChart = new ApexCharts(document.querySelector("#store-chart"), storeOptions);
            storeChart.render();

            // 3. Top Products Horizontal Bar Chart
            var productOptions = {
                ...chartOptions,
                chart: { type: 'bar', height: 180, animations: { enabled: false }, toolbar: { show: false } },
                plotOptions: { bar: { horizontal: true, borderRadius: 2 } },
                colors: ['#0d9488'],
                series: [{ name: 'Jumlah Unit', data: {!! json_encode($topProducts['series']) !!} }],
                xaxis: { categories: {!! json_encode($topProducts['labels']) !!}, labels: { show: false } },
                yaxis: { labels: { style: { colors: initialTheme === 'light' ? '#64748b' : '#94a3b8', fontSize: '11px', fontWeight: 600 } } },
                grid: { show: false }
            };
            var productChart = new ApexCharts(document.querySelector("#product-chart"), productOptions);
            productChart.render();

            // 4. Realtime Theme Toggling Listener
            window.addEventListener('theme-changed', function (e) {
                var mode = e.detail.theme;
                var textColor = mode === 'light' ? '#64748b' : '#94a3b8';
                var gridColor = mode === 'light' ? '#e2e8f0' : '#334155';

                if (typeof trendChart !== 'undefined') {
                    trendChart.updateOptions({ theme: { mode: mode }, grid: { borderColor: gridColor }, xaxis: { labels: { style: { colors: textColor } } }, yaxis: { labels: { style: { colors: textColor } } } });
                }

                if (typeof storeChart !== 'undefined') {
                    storeChart.updateOptions({ theme: { mode: mode }, legend: { labels: { colors: textColor } } });
                }

                if (typeof productChart !== 'undefined') {
                    productChart.updateOptions({ theme: { mode: mode }, yaxis: { labels: { style: { colors: textColor } } } });
                }
            });

            // 5. AJAX Polling for Real-Time Dashboard Updates
            setInterval(function () {
                fetch('{{ route("api.dashboard.stats") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        // Update Stat Cards
                        const statHeaders = document.querySelectorAll('.stat-grid-mobile h3');
                        if (statHeaders.length >= 4) {
                            statHeaders[0].innerText = 'Rp ' + data.stats.today;
                            statHeaders[1].innerText = 'Rp ' + data.stats.week;
                            statHeaders[2].innerText = 'Rp ' + data.stats.month;
                            statHeaders[3].innerText = 'Rp ' + data.stats.year;
                        }

                        // Update ApexCharts
                        if (typeof trendChart !== 'undefined') {
                            trendChart.updateSeries([{ name: 'Total Belanja (Rp)', data: data.spendingTrend.series }]);
                            trendChart.updateOptions({ xaxis: { categories: data.spendingTrend.labels } });
                        }
                        if (typeof storeChart !== 'undefined') {
                            storeChart.updateSeries(data.storeSpending.series);
                            storeChart.updateOptions({ labels: data.storeSpending.labels });
                        }
                        if (typeof productChart !== 'undefined') {
                            productChart.updateSeries([{ name: 'Jumlah Unit', data: data.topProducts.series }]);
                            productChart.updateOptions({ xaxis: { categories: data.topProducts.labels } });
                        }

                        // Update Recent Transactions: Desktop Table
                        const desktopTbody = document.getElementById('recent-transactions-desktop');
                        if (desktopTbody) {
                            let html = '';
                            if (data.recentTransactions.length === 0) {
                                html = `<tr><td colspan="6" class="py-8 text-center text-slate-500 font-medium">Belum ada catatan pengeluaran.</td></tr>`;
                            } else {
                                const colors = ['bg-blue-600', 'bg-emerald-600', 'bg-amber-500', 'bg-red-500', 'bg-purple-600', 'bg-pink-600', 'bg-cyan-600', 'bg-orange-500'];
                                data.recentTransactions.forEach(tx => {
                                    let imgHtml = '';
                                    if (tx.receipt_image) {
                                        imgHtml = `<img src="${tx.receipt_image}" alt="Struk" class="w-full h-full object-cover">`;
                                    } else {
                                        const charVal = tx.initial.charCodeAt(0) || 83;
                                        const colorClass = colors[charVal % colors.length];
                                        imgHtml = `<div class="w-full h-full flex items-center justify-center font-bold text-white text-sm ${colorClass}">${tx.initial}</div>`;
                                    }

                                    const statusBadge = tx.total_num == 0
                                        ? `<span class="badge badge-warning flex items-center gap-1.5 max-w-fit">Koreksi</span>`
                                        : `<span class="badge badge-success flex items-center gap-1.5 max-w-fit">Verif</span>`;

                                    html += `
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-2 pl-2">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 cursor-pointer border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800" 
                                                 onclick="window.location.href='/receipts?edit=${tx.id}'" 
                                                 title="Klik untuk Detail / Koreksi">
                                                ${imgHtml}
                                            </div>
                                        </td>
                                        <td class="py-3.5 font-mono text-xs text-slate-500 dark:text-slate-400">
                                            <a href="/receipts?edit=${tx.id}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">${tx.receipt_code}</a>
                                        </td>
                                        <td class="py-3.5 font-bold text-slate-900 dark:text-white font-display text-sm">
                                            <a href="/receipts?edit=${tx.id}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">${tx.store_name}</a>
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500 dark:text-slate-400">${tx.receipt_date}</td>
                                        <td class="py-3.5 text-xs">${statusBadge}</td>
                                        <td class="py-3.5 text-right font-bold text-slate-900 dark:text-white pr-2 font-display">Rp ${tx.total_price}</td>
                                    </tr>`;
                                });
                            }
                            desktopTbody.innerHTML = html;
                        }

                        // Update Recent Transactions: Mobile Cards
                        const mobileContainer = document.getElementById('recent-transactions-mobile');
                        if (mobileContainer) {
                            let html = '<div class="divide-y divide-slate-100 dark:divide-slate-800">';
                            if (data.recentTransactions.length === 0) {
                                html += `<div class="py-8 text-center text-slate-500 font-medium text-xs">Belum ada catatan pengeluaran.</div>`;
                            } else {
                                const colors = ['bg-blue-600', 'bg-emerald-600', 'bg-amber-500', 'bg-red-500', 'bg-purple-600', 'bg-pink-600', 'bg-cyan-600', 'bg-orange-500'];
                                data.recentTransactions.forEach(tx => {
                                    let imgHtml = '';
                                    if (tx.receipt_image) {
                                        imgHtml = `<img src="${tx.receipt_image}" alt="Struk" class="w-full h-full object-cover">`;
                                    } else {
                                        const charVal = tx.initial.charCodeAt(0) || 83;
                                        const colorClass = colors[charVal % colors.length];
                                        imgHtml = `<div class="w-full h-full flex items-center justify-center font-bold text-white text-sm ${colorClass}">${tx.initial}</div>`;
                                    }

                                    const statusText = tx.total_num == 0
                                        ? `<span class="text-amber-500">Koreksi</span>`
                                        : `<span class="text-emerald-500">Verif</span>`;

                                    html += `
                                    <div class="flex items-center justify-between py-3 px-1 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition duration-200 cursor-pointer"
                                         onclick="window.location.href='/receipts?edit=${tx.id}'">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                                ${imgHtml}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display truncate">${tx.store_name}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[11px] text-slate-500">${tx.receipt_date}</span>
                                                    <span class="text-[10px] font-bold">${statusText}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white font-display">Rp ${tx.total_price}</span>
                                            <div class="text-[9px] font-mono text-slate-500 mt-0.5">${tx.receipt_code}</div>
                                        </div>
                                    </div>`;
                                });
                            }
                            html += '</div>';
                            mobileContainer.innerHTML = html;
                        }

                        // Update Activity Logs
                        const logsContainer = document.getElementById('activity-logs-container');
                        if (logsContainer) {
                            let html = '';
                            if (data.activityLogs.length === 0) {
                                html = `<span class="text-xs text-slate-500 font-medium">Belum ada aktivitas tercatat.</span>`;
                            } else {
                                data.activityLogs.forEach(log => {
                                    html += `
                                    <div class="flex gap-2 text-xs">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 mt-1 shrink-0"></span>
                                        <div class="flex flex-col overflow-hidden">
                                            <span class="font-bold text-slate-700 dark:text-slate-300 truncate">${log.activity}</span>
                                            <span class="text-[10px] text-slate-500 mt-0.5">${log.time}</span>
                                        </div>
                                    </div>`;
                                });
                            }
                            logsContainer.innerHTML = html;
                        }
                    })
                    .catch(err => console.error("Poll Error:", err));
            }, 6000); // Poll every 6 seconds
        </script>
    @endpush
</x-app-layout>