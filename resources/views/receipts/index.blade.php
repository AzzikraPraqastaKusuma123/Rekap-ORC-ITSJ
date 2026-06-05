<x-app-layout>
    
    <!-- Title and Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Riwayat Struk Belanja</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola, verifikasi, koreksi, dan ekspor riwayat struk belanja Anda.</p>
        </div>
        
        <!-- Export Action Buttons Group -->
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Print / PDF trigger -->
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 border border-slate-800 bg-slate-900/60 hover:bg-slate-800 transition flex items-center gap-1.5" title="Cetak Riwayat">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak / PDF
            </button>

            <!-- Native CSV -->
            <a href="{{ route('receipts.export.csv', request()->query()) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 border border-slate-800 bg-slate-900/60 hover:bg-slate-800 transition flex items-center gap-1.5" title="Export CSV">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                CSV
            </a>

            <!-- Native Excel -->
            <a href="{{ route('receipts.export.excel', request()->query()) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 border border-slate-800 bg-slate-900/60 hover:bg-slate-800 transition flex items-center gap-1.5" title="Export Excel">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Excel
            </a>
        </div>
    </div>

    <!-- ====== FILTERS & SEARCH ROW ====== -->
    <div class="glass-card rounded-2xl p-6 mb-8">
        <form method="GET" action="{{ route('receipts.index') }}" class="m-0 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Search bar -->
                <div class="relative">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Kata Kunci</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama toko, kode struk..." 
                               class="w-full pl-10 pr-4 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                    </div>
                </div>

                <!-- Store Filter Dropdown -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Filter Toko</label>
                    <select name="store" class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                        <option value="">Semua Toko</option>
                        @foreach($stores as $st)
                            <option value="{{ $st }}" {{ (isset($filters['store']) && $filters['store'] === $st) ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filters -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" 
                               class="w-full px-3 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" 
                               class="w-full px-3 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                    </div>
                </div>

                <!-- Action & Clear Buttons -->
                <div class="flex items-end gap-2.5">
                    <button type="submit" class="flex-1 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('receipts.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-400 bg-slate-800 hover:bg-slate-700 transition" title="Reset Filters">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- ====== GALLERY CARD LIST ====== -->
    <div x-data="receiptModalModel()" x-init="@if(isset($editReceipt) && $editReceipt) loadReceipt({{ json_encode($editReceipt) }}) @endif">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($receipts as $receipt)
                <div class="glass-card rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-slate-700/80 flex flex-col group">
                    
                    <!-- Image Card Thumb -->
                    <div class="h-44 bg-slate-950 relative overflow-hidden shrink-0">
                        <img src="{{ asset('storage/' . $receipt->receipt_image) }}" alt="Receipt" 
                             class="w-full h-full object-cover object-top transition duration-500 group-hover:scale-105" loading="lazy">
                        
                        <!-- Floating Date Stamp -->
                        <div class="absolute bottom-3 left-3 bg-slate-950/70 backdrop-blur border border-white/5 px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wide text-white">
                            {{ Carbon\Carbon::parse($receipt->receipt_date)->translatedFormat('d M Y') }}
                        </div>
                        
                        <!-- Floating Edit Tag -->
                        <div class="absolute top-3 right-3 flex items-center gap-1.5">
                            <button @click="loadReceipt({{ json_encode($receipt->load('items')) }})" 
                                    class="w-8 h-8 rounded-lg bg-blue-600/90 text-white flex items-center justify-center hover:bg-blue-500 transition shadow" title="Open & Verify Details">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Details content body -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-bold text-white tracking-wide truncate pr-2">{{ $receipt->store_name }}</h3>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-900 border border-slate-800 px-2 py-0.5 rounded-md font-mono shrink-0">
                                    {{ $receipt->receipt_code }}
                                </span>
                            </div>
                            
                            <!-- Items counts -->
                            <p class="text-xs text-slate-400 font-semibold mb-4">{{ $receipt->items->count() }} item belanjaan</p>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-800/40 shrink-0">
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Total Belanja</span>
                                <span class="text-base font-black text-cyan-400">Rp {{ number_format($receipt->total_price, 0, ',', '.') }}</span>
                            </div>

                            <form method="POST" action="{{ route('receipts.delete', $receipt->id) }}" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus struk belanja ini secara permanen? Semua data barang yang terhubung akan terhapus secara permanen.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors" title="Delete Receipt">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full glass-card rounded-2xl p-12 text-center text-slate-500 font-semibold">
                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    Struk belanja tidak ditemukan. Silakan periksa kembali kata kunci pencarian Anda atau unggah struk baru melalui Telegram Bot!
                </div>
            @endforelse
        </div>

        <!-- ====== PAGINATION LINKS ====== -->
        <div class="px-2">
            {{ $receipts->appends(request()->query())->links() }}
        </div>

        <!-- ====== INTERACTIVE ALPINE DETAIL & VERIFICATION MODAL ====== -->
        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="open" style="display: none;" x-transition>
            <!-- Overlay dark blur -->
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="open = false"></div>

            <!-- Modal Frame -->
            <div class="relative min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
                <div class="relative glass-card rounded-3xl w-full max-w-6xl overflow-hidden shadow-2xl flex flex-col md:flex-row border border-slate-700/40 bg-slate-900/95" 
                     @keydown.escape.window="open = false">
                    
                    <!-- LEFT COLUMN: Zoomable & Drag-pannable Image Panel -->
                    <div class="w-full md:w-1/2 bg-slate-950/60 flex flex-col border-b md:border-b-0 md:border-r border-slate-800 overflow-hidden select-none relative group h-[400px] md:h-auto min-h-[400px]">
                        
                        <!-- Toolbar Controls -->
                        <div class="absolute top-4 left-4 z-30 flex items-center gap-1.5 bg-slate-900/80 backdrop-blur border border-slate-800 p-1.5 rounded-xl">
                            <button type="button" @click="zoomIn()" class="w-8 h-8 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center justify-center font-extrabold text-sm" title="Zoom In">+</button>
                            <button type="button" @click="zoomOut()" class="w-8 h-8 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center justify-center font-extrabold text-sm" title="Zoom Out">-</button>
                            <button type="button" @click="resetZoom()" class="w-8 h-8 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center justify-center text-xs font-bold" title="Reset Zoom">RESET</button>
                        </div>

                        <!-- Image Display viewport -->
                        <div class="flex-1 relative overflow-hidden flex items-center justify-center cursor-grab" 
                             :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
                             @wheel.prevent="
                                const delta = $event.deltaY;
                                if (delta < 0) { zoomIn(); } else { zoomOut(); }
                             "
                             @mousedown="
                                isDragging = true;
                                startX = $event.clientX - panX;
                                startY = $event.clientY - panY;
                             "
                             @mousemove="
                                if (!isDragging) return;
                                panX = $event.clientX - startX;
                                panY = $event.clientY - startY;
                             "
                             @mouseup="isDragging = false"
                             @mouseleave="isDragging = false">
                            
                            <img :src="imageUrl" alt="Receipt Full Image" 
                                 class="max-w-[85%] max-h-[85%] object-contain select-none pointer-events-none transition-transform duration-75 origin-center"
                                 :style="`transform: translate(${panX}px, ${panY}px) scale(${zoomScale});`" />
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Interactive verification and line items form editor -->
                    <div class="w-full md:w-1/2 p-6 md:p-8 flex flex-col max-h-[80vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-white tracking-wide">Verifikasi Struk Manual</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Koreksi nama toko, tanggal transaksi, atau daftar barang jika pemindai OCR kurang akurat.</p>
                            </div>
                            <button type="button" @click="open = false" class="text-slate-400 hover:text-white text-lg font-bold p-1 bg-slate-800/40 hover:bg-slate-800 rounded-lg w-8 h-8 flex items-center justify-center shrink-0">
                                &times;
                            </button>
                        </div>

                        <form :action="`/receipts/${receiptId}/update`" method="POST" class="m-0 space-y-6">
                            @csrf
                            
                            <!-- Store & Date -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Toko</label>
                                    <input type="text" name="store_name" x-model="storeName" required 
                                           class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                                    <input type="date" name="receipt_date" x-model="receiptDate" required 
                                           class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950/80 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition">
                                </div>
                            </div>

                            <!-- Dynamic Items Row Editor -->
                            <div>
                                <div class="flex items-center justify-between mb-3.5">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Daftar Item Belanjaan</label>
                                    <button type="button" @click="addItem()" class="px-2.5 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600 border border-blue-500/20 text-blue-400 hover:text-white text-xs font-bold transition flex items-center gap-1">
                                        + Tambah Item
                                    </button>
                                </div>

                                <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex items-center gap-2 bg-slate-950/40 p-2 rounded-xl border border-slate-800/40">
                                            <!-- Item Name -->
                                            <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" required placeholder="Nama Barang" 
                                                   class="flex-1 min-w-0 px-2.5 py-1 text-xs rounded-lg bg-slate-950 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition">
                                            
                                            <!-- Qty -->
                                            <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" required min="1" placeholder="Unit" 
                                                   class="w-12 px-1.5 py-1 text-xs text-center rounded-lg bg-slate-950 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition">
                                            
                                            <!-- Unit Price -->
                                            <input type="number" :name="`items[${index}][price]`" x-model.number="item.price" required placeholder="Harga" 
                                                   class="w-24 px-2 py-1 text-xs rounded-lg bg-slate-950 border border-slate-800/80 text-white focus:border-blue-500 focus:outline-none transition">

                                            <!-- Row delete trigger -->
                                            <button type="button" @click="removeItem(index)" class="p-1 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition" title="Hapus Baris">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Total Price & Math aggregates -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Total Perhitungan</span>
                                    <span class="text-sm font-bold text-slate-300">Rp <span x-text="calculateSubtotalSum().toLocaleString('id-ID')"></span></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col text-right">
                                        <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Total Terbaca</span>
                                        <span class="text-lg font-black text-cyan-400">Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span></span>
                                    </div>
                                    <!-- Keep a hidden input to post final total price -->
                                    <input type="hidden" name="total_price" :value="totalPrice">
                                    <!-- A button to sync total to calculated sum if manual updates mismatch -->
                                    <button type="button" @click="totalPrice = calculateSubtotalSum()" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition" title="Sync with Calculated sum">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Raw OCR text terminal block (collapsible) -->
                            <div x-data="{ collapsed: true }">
                                <button type="button" @click="collapsed = !collapsed" class="flex items-center justify-between w-full text-[10px] font-bold text-slate-500 uppercase tracking-wider hover:text-slate-300 transition">
                                    <span>Hasil Pemindaian Mentah (OCR)</span>
                                    <span x-text="collapsed ? 'Tampilkan' : 'Sembunyikan'">Tampilkan</span>
                                </button>
                                <div class="mt-2" x-show="!collapsed" x-transition>
                                    <pre class="bg-slate-950 p-4 rounded-xl text-[10px] font-mono text-slate-400 border border-slate-800/80 max-h-[140px] overflow-y-auto whitespace-pre-wrap select-text leading-relaxed" x-text="rawText"></pre>
                                </div>
                            </div>

                            <!-- Submit action buttons -->
                            <div class="flex items-center gap-3 pt-4 border-t border-slate-800/60">
                                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/20">
                                    Verifikasi & Simpan
                                </button>
                                <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-400 bg-slate-800 hover:bg-slate-700 transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Inject Alpine.js data model specifically for zoomable, pannable editable modal -->
    @push('scripts')
        <script>
            function receiptModalModel() {
                return {
                    open: false,
                    receiptId: null,
                    storeName: '',
                    receiptDate: '',
                    totalPrice: 0,
                    items: [],
                    imageUrl: '',
                    rawText: '',
                    zoomScale: 1,
                    panX: 0,
                    panY: 0,
                    isDragging: false,
                    startX: 0,
                    startY: 0,

                    // Load details into state
                    loadReceipt(receipt) {
                        this.receiptId = receipt.id;
                        this.storeName = receipt.store_name;
                        this.receiptDate = receipt.receipt_date;
                        this.totalPrice = parseFloat(receipt.total_price);
                        this.imageUrl = "{{ asset('storage') }}/" + receipt.receipt_image;
                        this.rawText = receipt.raw_text || '';
                        
                        // Map items
                        this.items = receipt.items.map(item => ({
                            item_name: item.item_name,
                            price: parseFloat(item.price),
                            qty: parseInt(item.qty)
                        }));

                        this.resetZoom();
                        this.open = true;
                    },

                    // Zoom operations
                    zoomIn() {
                        this.zoomScale = Math.min(this.zoomScale + 0.25, 4);
                    },
                    zoomOut() {
                        this.zoomScale = Math.max(this.zoomScale - 0.25, 0.5);
                    },
                    resetZoom() {
                        this.zoomScale = 1;
                        this.panX = 0;
                        this.panY = 0;
                        this.isDragging = false;
                    },

                    // Dynamic items modifiers
                    addItem() {
                        this.items.push({
                            item_name: '',
                            price: 0,
                            qty: 1
                        });
                    },
                    removeItem(index) {
                        this.items.splice(index, 1);
                        this.totalPrice = this.calculateSubtotalSum();
                    },
                    
                    // Math aggregates helper
                    calculateSubtotalSum() {
                        return this.items.reduce((sum, item) => {
                            const p = parseFloat(item.price) || 0;
                            const q = parseInt(item.qty) || 1;
                            return sum + (p * q);
                        }, 0);
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
