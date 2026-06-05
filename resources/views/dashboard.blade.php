<x-app-layout>
    @push('head')
        <!-- Load ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush

    <!-- Header Greeting Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight font-display">Ikhtisar Keuangan</h1>
        <p class="text-sm text-slate-400 mt-1">Laporan terpadu hasil ekstraksi data struk belanja digital secara instan.</p>
    </div>

    <!-- ====== 1. STATISTIC CARDS ROW ====== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card: Spent Today -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-slate-700/80 group">
            <span class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10"></span>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold text-slate-400 tracking-wider uppercase font-display">Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold text-white tracking-tight font-display">Rp {{ number_format($stats['today']['total'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1.5 mt-2">
                    @if($stats['today']['change_percentage'] > 0)
                        <span class="text-xs font-semibold text-rose-400 flex items-center gap-0.5">
                            ▲ {{ abs($stats['today']['change_percentage']) }}%
                        </span>
                    @elseif($stats['today']['change_percentage'] < 0)
                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-0.5">
                            ▼ {{ abs($stats['today']['change_percentage']) }}%
                        </span>
                    @else
                        <span class="text-xs font-semibold text-slate-400">0%</span>
                    @endif
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">dari hari sebelumnya</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Week -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-slate-700/80 group">
            <span class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10"></span>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold text-slate-400 tracking-wider uppercase font-display">Minggu Ini</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold text-white tracking-tight font-display">Rp {{ number_format($stats['week']['total'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1.5 mt-2">
                    @if($stats['week']['change_percentage'] > 0)
                        <span class="text-xs font-semibold text-rose-400 flex items-center gap-0.5">
                            ▲ {{ abs($stats['week']['change_percentage']) }}%
                        </span>
                    @elseif($stats['week']['change_percentage'] < 0)
                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-0.5">
                            ▼ {{ abs($stats['week']['change_percentage']) }}%
                        </span>
                    @else
                        <span class="text-xs font-semibold text-slate-400">0%</span>
                    @endif
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs pekan lalu</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Month -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-slate-700/80 group">
            <span class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10"></span>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold text-slate-400 tracking-wider uppercase font-display">Bulan Ini</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold text-white tracking-tight font-display">Rp {{ number_format($stats['month']['total'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1.5 mt-2">
                    @if($stats['month']['change_percentage'] > 0)
                        <span class="text-xs font-semibold text-rose-400 flex items-center gap-0.5">
                            ▲ {{ abs($stats['month']['change_percentage']) }}%
                        </span>
                    @elseif($stats['month']['change_percentage'] < 0)
                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-0.5">
                            ▼ {{ abs($stats['month']['change_percentage']) }}%
                        </span>
                    @else
                        <span class="text-xs font-semibold text-slate-400">0%</span>
                    @endif
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs bulan sebelumnya</span>
                </div>
            </div>
        </div>

        <!-- Card: Spent This Year -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-slate-700/80 group">
            <span class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10"></span>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold text-slate-400 tracking-wider uppercase font-display">Tahun Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.252.58 1.802l-3.97 2.887a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.887a1 1 0 00-1.176 0l-3.97 2.887c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.97-2.887c-.78-.55-.38-1.81.58-1.802h4.907a1 1 0 00.95-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold text-white tracking-tight font-display">Rp {{ number_format($stats['year']['total'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-1.5 mt-2">
                    @if($stats['year']['change_percentage'] > 0)
                        <span class="text-xs font-semibold text-rose-400 flex items-center gap-0.5">
                            ▲ {{ abs($stats['year']['change_percentage']) }}%
                        </span>
                    @elseif($stats['year']['change_percentage'] < 0)
                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-0.5">
                            ▼ {{ abs($stats['year']['change_percentage']) }}%
                        </span>
                    @else
                        <span class="text-xs font-semibold text-slate-400">0%</span>
                    @endif
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider font-display">vs tahun sebelumnya</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== 2. CHARTS DISPLAY ROW ====== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Spending Trend Area Chart -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-white tracking-tight font-display">Grafik Fluktuasi Harian</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Analisis akumulasi transaksi belanja harian Anda selama 15 hari terakhir.</p>
                </div>
                <span class="text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2.5 py-1 rounded-lg font-bold tracking-wider uppercase font-display">REALTIME</span>
            </div>
            
            <div id="trend-chart" class="w-full h-80"></div>
        </div>

        <!-- Store Shares Donut Chart -->
        <div class="glass-card rounded-2xl p-6 flex flex-col">
            <div class="mb-6">
                <h3 class="text-base font-bold text-white tracking-tight font-display">Distribusi Merchant & Toko</h3>
                <p class="text-xs text-slate-400 mt-0.5">Peta pembagian anggaran pengeluaran berdasarkan merchant atau toko ritel.</p>
            </div>
            
            <div class="flex-1 flex items-center justify-center">
                <div id="store-chart" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- ====== 3. RECENT TRANSACTIONS & LOG TIMELINES ====== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Transactions Table -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-white tracking-tight font-display">Riwayat Transaksi Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Struk belanja terbaru yang berhasil diekstraksi dan diverifikasi oleh sistem.</p>
                </div>
                <a href="{{ route('receipts.index') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-1 font-display">
                    Lihat Semua Riwayat
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-bold uppercase text-[10px] tracking-wider font-display">
                            <th class="pb-3 pl-2">Foto</th>
                            <th class="pb-3">Kode Transaksi</th>
                            <th class="pb-3">Merchant</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3 text-right pr-2">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-slate-300">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-2 pl-2">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-800/80 bg-slate-950 shrink-0">
                                        <img src="{{ asset('storage/' . $tx->receipt_image) }}" alt="Struk" 
                                             class="w-full h-full object-cover object-top hover:scale-110 transition duration-300 cursor-pointer" 
                                             onclick="window.location.href='{{ route('receipts.index', ['edit' => $tx->id]) }}'" 
                                             title="Klik untuk Detail / Koreksi">
                                    </div>
                                </td>
                                <td class="py-3.5 pl-2 font-mono text-xs text-slate-400">
                                    <a href="{{ route('receipts.index', ['edit' => $tx->id]) }}" class="hover:text-blue-400 transition">
                                        <code>{{ $tx->receipt_code }}</code>
                                    </a>
                                </td>
                                <td class="py-3.5 font-bold text-white font-display text-sm">
                                    <a href="{{ route('receipts.index', ['edit' => $tx->id]) }}" class="hover:text-blue-400 transition">
                                        {{ $tx->store_name }}
                                    </a>
                                </td>
                                <td class="py-3.5 text-xs text-slate-400">
                                    {{ Carbon\Carbon::parse($tx->receipt_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 text-right font-bold text-cyan-400 pr-2 font-display">
                                    Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold">
                                    Belum ada catatan pengeluaran. Hubungkan akun Telegram Anda dan kirim foto struk belanja untuk memulai otomatisasi rekap.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products Bar Chart -->
        <div class="glass-card rounded-2xl p-6 flex flex-col">
            <div class="mb-4">
                <h3 class="text-base font-bold text-white tracking-tight font-display">Barang Paling Sering Dibeli</h3>
                <p class="text-xs text-slate-400 mt-0.5">Visualisasi frekuensi pembelian barang berdasarkan volume kuantitas unit.</p>
            </div>
            
            <div class="flex-1 flex flex-col justify-between">
                <div id="product-chart" class="w-full"></div>
                
                <!-- Live Logs / Activity timeline header -->
                <div class="mt-4 pt-4 border-t border-slate-800/60">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3 font-display">Log Aktivitas</h4>
                    <div class="space-y-3.5">
                        @forelse($activityLogs as $log)
                            <div class="flex gap-2 text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 shrink-0 animate-pulse"></span>
                                <div class="flex flex-col overflow-hidden">
                                    <span class="font-bold text-slate-300 truncate">{{ $log->activity }}</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <span class="text-xs text-slate-500 font-semibold">Sistem siap beroperasi. Belum ada aktivitas tercatat.</span>
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

            // 1. Daily Expenditure Trend Area Chart
            var trendOptions = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    background: 'transparent'
                },
                theme: { mode: initialTheme },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#3b82f6']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.02,
                        stops: [0, 90, 100],
                        colorStops: [
                            { offset: 0, color: '#3b82f6', opacity: 0.35 },
                            { offset: 100, color: '#3b82f6', opacity: 0.01 }
                        ]
                    }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: initialTheme === 'light' ? 'rgba(15, 23, 42, 0.06)' : 'rgba(255, 255, 255, 0.05)',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                series: [{
                    name: 'Total Belanja (Rp)',
                    data: {!! json_encode($spendingTrend['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($spendingTrend['labels']) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            colors: initialTheme === 'light' ? '#475569' : '#94a3b8',
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: initialTheme === 'light' ? '#475569' : '#94a3b8',
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif'
                        },
                        formatter: function (value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                tooltip: {
                    theme: initialTheme,
                    y: {
                        formatter: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            };
            var trendChart = new ApexCharts(document.querySelector("#trend-chart"), trendOptions);
            trendChart.render();

            // 2. Store Spending Shares Donut Chart
            var storeOptions = {
                chart: {
                    type: 'donut',
                    height: 280,
                    background: 'transparent'
                },
                theme: { mode: initialTheme },
                stroke: { show: false },
                labels: {!! json_encode($storeSpending['labels']) !!},
                series: {!! json_encode($storeSpending['series']) !!},
                colors: ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ec4899'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                                    color: initialTheme === 'light' ? '#475569' : '#94a3b8',
                                    offsetY: -8
                                },
                                value: {
                                    show: true,
                                    fontSize: '18px',
                                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                                    color: initialTheme === 'light' ? '#0f172a' : '#ffffff',
                                    fontWeight: 'bold',
                                    offsetY: 6,
                                    formatter: function (val) {
                                        return 'Rp ' + parseInt(val).toLocaleString('id-ID');
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Belanja',
                                    color: initialTheme === 'light' ? '#475569' : '#94a3b8',
                                    fontSize: '11px',
                                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                                    fontWeight: 'bold',
                                    formatter: function (w) {
                                        var total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        if (total >= 1000000) {
                                            return 'Rp ' + (total / 1000000).toFixed(1) + 'jt';
                                        }
                                        return 'Rp ' + total.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    fontSize: '11px',
                    fontFamily: 'Inter, sans-serif',
                    markers: { radius: 12 },
                    labels: { colors: initialTheme === 'light' ? '#475569' : '#94a3b8' }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            };
            var storeChart = new ApexCharts(document.querySelector("#store-chart"), storeOptions);
            storeChart.render();

            // 3. Top Products Horizontal Bar Chart
            var productOptions = {
                chart: {
                    type: 'bar',
                    height: 180,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                theme: { mode: initialTheme },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '45%',
                        borderRadius: 4,
                        colors: {
                            ranges: [{ from: 0, to: 100, color: '#06b6d4' }]
                        }
                    }
                },
                stroke: { show: false },
                grid: { show: false },
                series: [{
                    name: 'Jumlah Unit',
                    data: {!! json_encode($topProducts['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($topProducts['labels']) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { show: false }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: initialTheme === 'light' ? '#475569' : '#94a3b8',
                            fontSize: '11px',
                            fontFamily: 'Plus Jakarta Sans, sans-serif',
                            fontWeight: 'bold'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#fff'],
                        fontFamily: 'Inter, sans-serif',
                        fontSize: '10px'
                    },
                    formatter: function (val, opt) {
                        return val + ' unit';
                    },
                    offsetX: 6
                },
                tooltip: {
                    theme: initialTheme,
                    y: {
                        formatter: function (value) {
                            return value + ' unit';
                        }
                    }
                }
            };
            var productChart = new ApexCharts(document.querySelector("#product-chart"), productOptions);
            productChart.render();

            // 4. Realtime Theme Toggling Listener
            window.addEventListener('theme-changed', function(e) {
                var mode = e.detail.theme;
                
                // Update Area Chart
                if (typeof trendChart !== 'undefined') {
                    trendChart.updateOptions({
                        theme: { mode: mode },
                        grid: { borderColor: mode === 'light' ? 'rgba(15, 23, 42, 0.06)' : 'rgba(255, 255, 255, 0.05)' },
                        xaxis: { labels: { style: { colors: mode === 'light' ? '#475569' : '#94a3b8' } } },
                        yaxis: { labels: { style: { colors: mode === 'light' ? '#475569' : '#94a3b8' } } },
                        tooltip: { theme: mode }
                    });
                }
                
                // Update Donut Chart
                if (typeof storeChart !== 'undefined') {
                    storeChart.updateOptions({
                        theme: { mode: mode },
                        legend: { labels: { colors: mode === 'light' ? '#475569' : '#94a3b8' } },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        name: { color: mode === 'light' ? '#475569' : '#94a3b8' },
                                        value: { color: mode === 'light' ? '#0f172a' : '#ffffff' },
                                        total: { color: mode === 'light' ? '#475569' : '#94a3b8' }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Update Bar Chart
                if (typeof productChart !== 'undefined') {
                    productChart.updateOptions({
                        theme: { mode: mode },
                        yaxis: { labels: { style: { colors: mode === 'light' ? '#475569' : '#94a3b8' } } },
                        tooltip: { theme: mode }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
