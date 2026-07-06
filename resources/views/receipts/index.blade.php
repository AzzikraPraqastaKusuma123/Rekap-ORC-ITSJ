<x-app-layout>

    {{-- ====== PAGE HEADER ====== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight font-display text-slate-900 dark:text-white">
                Riwayat Struk Belanja
            </h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola, verifikasi, koreksi, dan ekspor seluruh data struk
                Anda.</p>
        </div>

        {{-- Export & Actions --}}
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            @if(auth()->user()->role === 'staff')
            <button type="button" @click="$dispatch('open-upload-modal')"
                class="btn-primary py-2 px-3 text-xs md:text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <span class="hidden sm:inline">Upload Web</span>
            </button>
            <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 hidden sm:block mx-1"></div>
            @endif
            <a href="{{ route('receipts.print', request()->query()) }}" target="_blank" class="btn-secondary flex items-center gap-2 py-2 px-3 text-xs md:text-sm"
                title="Cetak Laporan (PDF)">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span class="hidden sm:inline">Cetak</span>
            </a>
            @if(in_array(auth()->user()->role, ['superadmin', 'admin']))
                <a href="{{ route('receipts.export.csv', request()->query()) }}"
                    class="btn-secondary flex items-center gap-2 py-2 px-3 text-xs md:text-sm" title="Export CSV">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    CSV
                </a>
                <a href="{{ route('receipts.export.excel', request()->query()) }}"
                    class="btn-secondary flex items-center gap-2 py-2 px-3 text-xs md:text-sm" title="Export Excel">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
            @endif
        </div>
    </div>

    {{-- ====== FILTERS & SEARCH ====== --}}
    <div x-data="{ showFilters: {{ request()->hasAny(['store', 'start_date', 'end_date']) ? 'true' : 'false' }} }"
        class="pro-card p-4 md:p-5 mb-6">
        <form method="GET" action="{{ route('receipts.index') }}" class="m-0">
            <!-- Top search & toggle row -->
            <div class="flex items-center gap-2">
                <!-- Search input -->
                <div class="flex-1">
                    <label
                        class="hidden md:block text-[10px] font-bold uppercase tracking-wider mb-1.5 text-slate-500">Cari
                        Struk</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            class="form-input !pl-10 text-sm" placeholder="Cari nama toko, kode...">
                    </div>
                </div>

                <!-- Desktop search button (inline) -->
                <div class="hidden md:flex items-end self-end h-[42px]">
                    <button type="submit" class="btn-primary h-full px-5 text-sm">Cari</button>
                </div>

                <!-- Mobile Filter Toggle Button -->
                <div class="md:hidden flex items-center h-[42px]">
                    <button type="button" @click="showFilters = !showFilters"
                        class="btn-secondary h-full px-3 text-sm flex items-center gap-1.5"
                        :class="showFilters ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10' : ''">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        <span>Filter</span>
                        @if(request()->hasAny(['store', 'start_date', 'end_date']))
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        @endif
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (collapsible on mobile, always visible on desktop) -->
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="md:!grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-4 pt-4 border-t border-slate-200 dark:border-slate-800"
                :class="showFilters ? 'grid' : 'hidden md:grid'">

                {{-- Store Filter --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider mb-1.5 text-slate-500">Filter
                        Toko</label>
                    <select name="store" class="form-input text-sm">
                        <option value="">Semua Toko</option>
                        @foreach($stores as $st)
                            <option value="{{ $st }}" {{ (isset($filters['store']) && $filters['store'] === $st) ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range: Start --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider mb-1.5 text-slate-500">Dari
                        Tanggal</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                        class="form-input text-sm">
                </div>

                {{-- Date Range: End --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider mb-1.5 text-slate-500">Sampai
                        Tanggal</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                        class="form-input text-sm">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider mb-1.5 text-slate-500">Status Laporan</label>
                    <select name="status" class="form-input text-sm font-semibold">
                        <option value="">Semua Status</option>
                        <option value="raw" {{ (isset($filters['status']) && $filters['status'] === 'raw') ? 'selected' : '' }}>🔴 Belum Diedit (Mentah)</option>
                        <option value="edited" {{ (isset($filters['status']) && $filters['status'] === 'edited') ? 'selected' : '' }}>🟡 Menunggu ACC (Diedit)</option>
                        <option value="verified" {{ (isset($filters['status']) && $filters['status'] === 'verified') ? 'selected' : '' }}>🟢 Valid (Di-ACC)</option>
                        <option value="rejected" {{ (isset($filters['status']) && $filters['status'] === 'rejected') ? 'selected' : '' }}>🟠 Ditolak / Koreksi</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2 h-[42px] self-end mt-2 lg:mt-0">
                    <button type="submit" class="btn-primary flex-1 h-full justify-center text-sm">
                        Terapkan
                    </button>
                    <a href="{{ route('receipts.index') }}"
                        class="btn-secondary h-full px-4 text-sm justify-center flex-1">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ====== RECEIPT CARDS GRID ====== --}}
    <div x-data="receiptModalModel()"
        x-init="@if(isset($editReceipt) && $editReceipt) loadReceipt({{ json_encode($editReceipt) }}) @endif">

        {{-- Results count --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold text-slate-500">
                Menampilkan <span class="font-bold text-slate-900 dark:text-white">{{ $receipts->count() }}</span>
                dari <span class="font-bold text-slate-900 dark:text-white">{{ $receipts->total() }}</span> struk
            </p>
            @if(request()->hasAny(['search', 'store', 'start_date', 'end_date']))
                <a href="{{ route('receipts.index') }}"
                    class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hapus Filter
                </a>
            @endif
        </div>

        {{-- Cards grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-5 mb-6">
            @forelse($receipts as $receipt)
                @php
                    $categoryIcons = [
                        'Food & Beverage' => '🍴',
                        'Groceries' => '🛒',
                        'Electronics' => '🔌',
                        'Utilities' => '💡',
                        'Fashion' => '👕',
                        'Medical' => '💊',
                        'Others' => '📦'
                    ];
                    $categoryIcon = $categoryIcons[$receipt->category] ?? '📦';

                    $itemCategoryIcons = [
                        'Food' => '🍔',
                        'Beverage' => '🥤',
                        'Snack' => '🍿',
                        'Household' => '🧹',
                        'Personal Care' => '🧴',
                        'Electronics' => '🔌',
                        'Clothing' => '👕',
                        'Medicine' => '💊',
                        'Others' => '📦'
                    ];
                @endphp
                <div class="bg-white dark:bg-slate-900 overflow-hidden flex flex-col group transition-all duration-300 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-200 dark:hover:border-blue-900 hover:-translate-y-1 cursor-pointer"
                    @click="loadReceipt({{ json_encode($receipt->load('items')) }})">

                    {{-- Receipt Image Thumbnail --}}
                    <div
                        class="relative h-32 overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800">
                        <img src="{{ asset('storage/' . $receipt->receipt_image) }}" alt="Struk {{ $receipt->store_name }}"
                            class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-110"
                            loading="lazy" decoding="async">

                        {{-- Gradient overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent">
                        </div>

                        {{-- Status badges --}}
                        <div class="absolute top-2 left-2 right-2 flex justify-between items-start">
                            @if($receipt->status === 'pending_correction')
                                <span
                                    class="flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-rose-500/90 backdrop-blur text-white shadow-sm ring-1 ring-white/20">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Koreksi
                                </span>
                            @elseif($receipt->status === 'pending_approval')
                                <span
                                    class="flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-500/90 backdrop-blur text-white shadow-sm ring-1 ring-white/20">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Menunggu ACC
                                </span>
                            @else
                                <span
                                    class="flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-500/90 backdrop-blur text-white shadow-sm ring-1 ring-white/20">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Valid
                                </span>
                            @endif
                            
                            @if($receipt->created_at->eq($receipt->updated_at))
                                <span class="px-2 py-0.5 ml-1 rounded-md text-[9px] font-bold bg-slate-800/90 backdrop-blur text-slate-300 shadow-sm ring-1 ring-white/10">
                                    🔴 Belum Diedit
                                </span>
                            @else
                                <span class="px-2 py-0.5 ml-1 rounded-md text-[9px] font-bold bg-blue-500/90 backdrop-blur text-white shadow-sm ring-1 ring-white/20">
                                    🟢 Sudah Diedit
                                </span>
                            @endif

                            @if(isset($receipt->confidence_score))
                                @php $score = $receipt->confidence_score;
                                $scoreBg = $score >= 80 ? 'text-emerald-300' : ($score >= 60 ? 'text-amber-300' : 'text-rose-300'); @endphp
                                <span
                                    class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-black/60 backdrop-blur {{ $scoreBg }} ring-1 ring-white/10">
                                    AI: {{ $score }}%
                                </span>
                            @endif
                        </div>

                        {{-- Lower info overlay --}}
                        <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between">
                            <span
                                class="px-1.5 py-0.5 rounded text-[9px] font-medium text-slate-200 bg-black/50 backdrop-blur">
                                {{ \Carbon\Carbon::parse($receipt->receipt_date)->translatedFormat('d M Y') }}
                            </span>
                            <span
                                class="px-1.5 py-0.5 rounded text-[9px] font-mono font-medium text-slate-300 bg-black/50 backdrop-blur">
                                {{ $receipt->receipt_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-3.5 flex-1 flex flex-col gap-2.5">
                        {{-- Store Header --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm font-bold text-slate-900 dark:text-white tracking-tight font-display truncate flex items-center gap-1.5">
                                    <span class="text-sm shrink-0">{{ $categoryIcon }}</span>
                                    <span class="truncate">{{ $receipt->store_name }}</span>
                                </h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $receipt->items->count() }} item
                                    dipindai</p>
                            </div>
                        </div>

                        {{-- Condensed Items Preview --}}
                        @if($receipt->items->count() > 0)
                            <div
                                class="bg-slate-50/50 dark:bg-slate-800/30 rounded border border-slate-100 dark:border-slate-800 py-1.5 px-2 flex-1">
                                <div class="space-y-1">
                                    @foreach($receipt->items->take(2) as $item)
                                        <div class="flex items-center justify-between gap-2 text-[10px]">
                                            <span class="truncate text-slate-600 dark:text-slate-400 font-medium">
                                                <span
                                                    class="opacity-0 w-0 hidden">{{ $itemCategoryIcons[$item->category] ?? '' }}</span>
                                                • {{ $item->item_name }}
                                            </span>
                                            <span class="font-bold text-slate-900 dark:text-slate-300 shrink-0">
                                                {{ $item->qty }}x <span
                                                    class="text-blue-600 dark:text-blue-400">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($receipt->items->count() > 2)
                                        <div
                                            class="text-[9px] font-bold text-slate-400 text-center pt-1 mt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            + {{ $receipt->items->count() - 2 }} item lainnya
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div
                                class="flex-1 flex items-center justify-center bg-amber-50/50 dark:bg-amber-900/10 rounded border border-amber-100 dark:border-amber-900/30">
                                <span class="text-[10px] font-medium text-amber-600 dark:text-amber-500">⚠ Belum ada
                                    rincian</span>
                            </div>
                        @endif

                        {{-- Tidy Footer --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-base font-black font-display text-slate-900 dark:text-white leading-none">
                                    <span
                                        class="text-[9px] text-slate-400 font-normal align-top">Rp</span>{{ number_format($receipt->total_price, 0, ',', '.') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('receipts.delete', $receipt->id) }}" class="m-0"
                                onsubmit="return confirm('Hapus struk ini permanen?');" @click.stop>
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                    title="Hapus Struk">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="pro-card p-12 text-center flex flex-col items-center">
                        <div
                            class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display mb-1">Tidak Ada Struk
                        </h3>
                        <p class="text-sm text-slate-500 max-w-sm">
                            Belum ada data struk. Hubungkan Bot Telegram atau gunakan Upload Web untuk memulai rekap.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ====== PAGINATION ====== --}}
        <div class="mt-6">
            {{ $receipts->appends(request()->query())->links() }}
        </div>

        {{-- ====== VERIFICATION MODAL ====== --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="open = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/90" @click="open = false"></div>

            {{-- Modal Panel --}}
            <div class="relative w-full max-w-5xl rounded-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh] bg-white dark:bg-slate-900 shadow-2xl"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100" @click.stop>

                {{-- LEFT: Image Panel --}}
                <div
                    class="w-full md:w-[45%] bg-slate-100 dark:bg-slate-950 flex flex-col relative overflow-hidden select-none min-h-[300px]">
                    {{-- Toolbar --}}
                    <div
                        class="absolute top-4 left-4 z-20 flex items-center gap-1 bg-white dark:bg-slate-800 rounded-lg p-1 shadow-sm border border-slate-200 dark:border-slate-700">
                        <button @click="zoomIn()"
                            class="w-8 h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md font-bold text-lg">+</button>
                        <button @click="zoomOut()"
                            class="w-8 h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md font-bold text-lg">−</button>
                        <div class="w-px h-4 bg-slate-200 dark:bg-slate-600 mx-1"></div>
                        <button @click="resetZoom()"
                            class="px-3 h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md text-[10px] font-bold uppercase tracking-wider">Reset</button>
                    </div>

                    {{-- Image viewport --}}
                    <div class="flex-1 flex items-center justify-center overflow-hidden"
                        :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
                        @wheel.prevent="$event.deltaY < 0 ? zoomIn() : zoomOut()"
                        @mousedown="isDragging=true;startX=$event.clientX-panX;startY=$event.clientY-panY"
                        @mousemove="if(isDragging){panX=$event.clientX-startX;panY=$event.clientY-startY}"
                        @mouseup="isDragging=false" @mouseleave="isDragging=false"
                        @touchstart.prevent="touch=$event.touches[0];isDragging=true;startX=touch.clientX-panX;startY=touch.clientY-panY"
                        @touchmove.prevent="if(isDragging){touch=$event.touches[0];panX=touch.clientX-startX;panY=touch.clientY-startY}"
                        @touchend="isDragging=false">
                        <img :src="imageUrl" alt="Struk"
                            class="max-w-[95%] max-h-[95%] object-contain pointer-events-none select-none"
                            :style="`transform: translate(${panX}px, ${panY}px) scale(${zoomScale});`">
                    </div>
                </div>

                {{-- RIGHT: Edit Form --}}
                <div class="flex-1 flex flex-col overflow-hidden border-l border-slate-200 dark:border-slate-800">
                    {{-- Modal Header --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Verifikasi &
                                    Koreksi</h3>
                                <template x-if="confidenceScore !== null">
                                    <span
                                        :class="confidenceScore >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400' : (confidenceScore >= 60 ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400')"
                                        class="px-2 py-0.5 rounded text-[10px] font-bold">
                                        🧠 AI Accuracy: <span x-text="confidenceScore"></span>%
                                    </span>
                                </template>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Periksa keakuratan data OCR</p>
                        </div>
                        <button @click="open = false"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-lg bg-slate-100 dark:bg-slate-800">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable Form Body --}}
                    <div class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-slate-900/50">
                        <form :action="`/receipts/${receiptId}/update`" method="POST" class="m-0 space-y-5"
                            id="receipt-update-form">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama
                                        Toko</label>
                                    <input type="text" name="store_name" x-model="storeName" required
                                        class="form-input text-sm bg-white dark:bg-slate-900">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal</label>
                                    <input type="date" name="receipt_date" x-model="receiptDate" required
                                        class="form-input text-sm bg-white dark:bg-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kategori</label>
                                    <select name="category" x-model="category"
                                        class="form-input text-sm bg-white dark:bg-slate-900">
                                        <option value="Food & Beverage">🍴 Food & Beverage</option>
                                        <option value="Groceries">🛒 Groceries</option>
                                        <option value="Electronics">🔌 Electronics</option>
                                        <option value="Utilities">💡 Utilities</option>
                                        <option value="Fashion">👕 Fashion</option>
                                        <option value="Medical">💊 Medical</option>
                                        <option value="Others">📦 Others</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pajak
                                        (Rp)</label>
                                    <input type="number" name="tax" x-model.number="tax"
                                        class="form-input text-sm bg-white dark:bg-slate-900">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Diskon
                                        (Rp)</label>
                                    <input type="number" name="discount" x-model.number="discount"
                                        class="form-input text-sm bg-white dark:bg-slate-900">
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                        Daftar Item
                                        <span
                                            class="ml-1 font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 px-1.5 py-0.5 rounded"
                                            x-text="items.length"></span>
                                    </label>
                                    <button type="button" @click="addItem()"
                                        class="btn-secondary py-1.5 px-3 text-xs bg-white dark:bg-slate-800">
                                        + Tambah
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div
                                            class="flex flex-col gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative group">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] font-bold text-slate-400"
                                                    x-text="`Item #${index + 1}`"></span>
                                                <button type="button" @click="removeItem(index)"
                                                    class="text-[10px] font-bold text-red-500 hover:text-red-600">
                                                    Hapus
                                                </button>
                                            </div>
                                            <input type="text" :name="`items[${index}][item_name]`"
                                                x-model="item.item_name" required placeholder="Nama Barang"
                                                class="form-input text-xs py-2 px-3 bg-slate-50 dark:bg-slate-950">
                                            <div class="grid grid-cols-3 gap-2">
                                                <div>
                                                    <label
                                                        class="block text-[9px] text-slate-400 font-bold mb-0.5">Kategori</label>
                                                    <select :name="`items[${index}][category]`" x-model="item.category"
                                                        class="form-input text-[10px] py-1.5 px-2 bg-slate-50 dark:bg-slate-950">
                                                        <option value="Food">🍔 Food</option>
                                                        <option value="Beverage">🥤 Beverage</option>
                                                        <option value="Snack">🍿 Snack</option>
                                                        <option value="Household">🧹 Household</option>
                                                        <option value="Personal Care">🧴 Personal Care</option>
                                                        <option value="Electronics">🔌 Electronics</option>
                                                        <option value="Clothing">👕 Clothing</option>
                                                        <option value="Medicine">💊 Medicine</option>
                                                        <option value="Others">📦 Others</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[9px] text-slate-400 font-bold mb-0.5">Qty</label>
                                                    <input type="number" :name="`items[${index}][qty]`"
                                                        x-model.number="item.qty" required min="1" placeholder="Qty"
                                                        class="form-input text-center text-xs py-1.5 px-2 bg-slate-50 dark:bg-slate-950">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[9px] text-slate-400 font-bold mb-0.5">Harga</label>
                                                    <input type="number" :name="`items[${index}][price]`"
                                                        x-model.number="item.price" required placeholder="Harga"
                                                        class="form-input text-xs py-1.5 px-2 bg-slate-50 dark:bg-slate-950">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="items.length === 0">
                                        <div
                                            class="p-4 text-center text-sm text-slate-500 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 border-dashed">
                                            Tidak ada item.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">
                                        Subtotal Kalkulasi</p>
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Rp <span x-text="calculateSubtotalSum().toLocaleString('id-ID')"></span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">
                                            Total Struk</p>
                                        <p class="text-lg font-black text-blue-600 dark:text-blue-400">
                                            Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span>
                                        </p>
                                    </div>
                                    <input type="hidden" name="total_price" :value="totalPrice">
                                    <button type="button" @click="totalPrice = calculateSubtotalSum()"
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20"
                                        title="Sinkronkan dengan kalkulasi">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 0121.21 8H18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Modal Footer Actions --}}
                    <div
                        class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex gap-3 shrink-0">
                        @if(in_array(auth()->user()->role, ['admin', 'staff']))
                            <div class="flex flex-col gap-3 w-full">
                                <button type="submit" form="receipt-update-form"
                                    class="w-full bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 border border-blue-200/50 dark:border-blue-800/50">
                                    💾 Simpan Perubahan Data
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <template x-if="status !== 'verified'">
                                        <div class="flex gap-3 w-full">
                                            <form :action="`/receipts/${receiptId}/reject`" method="POST" class="flex-1 m-0">
                                                @csrf
                                                <button type="submit" class="w-full bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 border border-rose-200/50 dark:border-rose-800/50">
                                                    ✗ Kembalikan & Tolak
                                                </button>
                                            </form>
                                            <form :action="`/receipts/${receiptId}/approve`" method="POST" class="flex-1 m-0">
                                                @csrf
                                                <button type="submit" class="w-full btn-primary justify-center py-2.5 bg-emerald-600 hover:bg-emerald-700">
                                                    ✓ ACC & Validasi
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="status === 'verified'">
                                        <div class="flex gap-3 w-full">
                                            <form :action="`/receipts/${receiptId}/reject`" method="POST" class="flex-1 m-0">
                                                @csrf
                                                <button type="submit" class="w-full bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 border border-rose-200/50 dark:border-rose-800/50">
                                                    ✗ Batalkan ACC (Tolak)
                                                </button>
                                            </form>
                                            <form :action="`/receipts/${receiptId}/approve`" method="POST" class="flex-1 m-0">
                                                @csrf
                                                <button type="submit" class="w-full h-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 border border-emerald-200/50 hover:bg-emerald-100 transition-colors">
                                                    ✅ Struk Telah di-ACC
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                @endif
                            </div>
                        @else
                            <div class="w-full text-center text-sm font-bold text-slate-400 py-2.5">
                                Tampilan Read-Only (Super Administrator)
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function receiptModalModel() {
                return {
                    open: false,
                    receiptId: null,
                    storeName: '',
                    receiptDate: '',
                    totalPrice: 0,
                    tax: 0,
                    discount: 0,
                    category: 'Others',
                    confidenceScore: null,
                    status: null,
                    items: [],
                    imageUrl: '',
                    rawText: '',
                    zoomScale: 1, panX: 0, panY: 0,
                    isDragging: false, startX: 0, startY: 0, touch: null,

                    loadReceipt(receipt) {
                        this.receiptId = receipt.id;
                        this.storeName = receipt.store_name;
                        this.receiptDate = receipt.receipt_date;
                        this.totalPrice = parseFloat(receipt.total_price);
                        this.tax = parseFloat(receipt.tax || 0);
                        this.status = receipt.status || 'verified';
                        this.discount = parseFloat(receipt.discount || 0);
                        this.category = receipt.category || 'Others';
                        this.confidenceScore = receipt.confidence_score !== null ? parseInt(receipt.confidence_score) : null;
                        this.imageUrl = "{{ asset('storage') }}/" + receipt.receipt_image;
                        this.rawText = receipt.raw_text || '';
                        this.items = (receipt.items || []).map(i => ({
                            item_name: i.item_name,
                            category: i.category || 'Others',
                            price: parseFloat(i.price),
                            qty: parseInt(i.qty)
                        }));
                        this.resetZoom();
                        this.open = true;
                    },
                    zoomIn() { this.zoomScale = Math.min(this.zoomScale + 0.25, 5); },
                    zoomOut() { this.zoomScale = Math.max(this.zoomScale - 0.25, 0.5); },
                    resetZoom() { this.zoomScale = 1; this.panX = 0; this.panY = 0; this.isDragging = false; },
                    addItem() { this.items.push({ item_name: '', category: 'Others', price: 0, qty: 1 }); },
                    removeItem(i) { this.items.splice(i, 1); this.totalPrice = this.calculateSubtotalSum(); },
                    calculateSubtotalSum() {
                        return this.items.reduce((s, i) => s + (parseFloat(i.price) || 0) * (parseInt(i.qty) || 1), 0);
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>