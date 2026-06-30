<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Struk Belanja</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (for styling simplicity, fallback classes match) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Telegram WebApp JS -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #030712;
            color: #e2e8f0;
        }
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>
<body class="min-h-screen pb-12 antialiased">
    
    <!-- Header banner -->
    <div class="px-4 py-4 border-b border-slate-800 bg-slate-950/80 flex items-center justify-between sticky top-0 z-50 backdrop-blur-md">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <h1 class="text-sm font-extrabold text-white tracking-tight">Koreksi Struk</h1>
                    @if(isset($receipt->confidence_score))
                        <span class="text-[8px] font-bold px-1 py-0.5 rounded bg-blue-500/20 text-blue-400">🧠 AI: {{ $receipt->confidence_score }}%</span>
                    @endif
                </div>
                <p class="text-[10px] text-slate-400">Kode: <span class="font-mono">{{ $receipt->receipt_code }}</span></p>
            </div>
        </div>
        <button onclick="closeWebApp()" class="text-xs font-bold text-slate-400 hover:text-white bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-lg transition">
            Batal
        </button>
    </div>

    <div class="max-w-md mx-auto px-4 mt-5 space-y-6" x-data="telegramEditModel()">
        
        <!-- Tab view toggle for Image vs Form on small mobile screens -->
        <div class="flex bg-slate-950/80 p-1 rounded-xl border border-slate-800">
            <button @click="tab = 'form'" 
                    class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all"
                    :class="tab === 'form' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white'">
                Formulir Data
            </button>
            <button @click="tab = 'image'" 
                    class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all"
                    :class="tab === 'image' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white'">
                Foto Bukti Struk
            </button>
        </div>

        <!-- IMAGE PREVIEW TAB -->
        <div x-show="tab === 'image'" class="glass-card rounded-2xl p-4 overflow-hidden relative" style="display: none;">
            <div class="h-96 bg-slate-950/60 rounded-xl overflow-hidden flex items-center justify-center relative cursor-grab">
                <img src="{{ asset('storage/' . $receipt->receipt_image) }}" alt="Receipt" 
                     class="max-w-full max-h-full object-contain rounded-lg pointer-events-none transition-transform duration-75"
                     :style="`transform: scale(${zoomScale});`" />
            </div>
            
            <div class="flex items-center justify-between mt-3.5">
                <span class="text-[10px] text-slate-500 font-bold uppercase">Gunakan cubitan untuk zoom foto</span>
                <div class="flex gap-2">
                    <button type="button" @click="zoomScale = Math.max(zoomScale - 0.25, 0.5)" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-bold text-sm">-</button>
                    <button type="button" @click="zoomScale = Math.min(zoomScale + 0.25, 4)" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-bold text-sm">+</button>
                    <button type="button" @click="zoomScale = 1" class="px-3 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 font-bold">RESET</button>
                </div>
            </div>
        </div>

        <!-- FORM EDITOR TAB -->
        <form x-show="tab === 'form'" action="{{ route('receipts.telegram-update', [$receipt->id, $hash]) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Store and Date Section -->
            <div class="glass-card rounded-2xl p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Merchant / Toko</label>
                    <input type="text" name="store_name" x-model="storeName" required 
                           class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Pembelian</label>
                    <input type="date" name="receipt_date" x-model="receiptDate" required 
                           class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                </div>
                <div class="grid grid-cols-3 gap-2 pt-2">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kategori</label>
                        <select name="category" x-model="category"
                                class="w-full px-2 py-1.5 text-xs rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                            <option value="Food & Beverage">🍴 F&B</option>
                            <option value="Groceries">🛒 Groceries</option>
                            <option value="Electronics">🔌 Electr.</option>
                            <option value="Utilities">💡 Utils</option>
                            <option value="Fashion">👕 Fashion</option>
                            <option value="Medical">💊 Medical</option>
                            <option value="Others">📦 Others</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pajak (Rp)</label>
                        <input type="number" name="tax" x-model.number="tax"
                               class="w-full px-2 py-1.5 text-xs rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Diskon (Rp)</label>
                        <input type="number" name="discount" x-model.number="discount"
                               class="w-full px-2 py-1.5 text-xs rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Items List -->
            <div class="glass-card rounded-2xl p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Daftar Barang Belanja</label>
                    <button type="button" @click="addItem()" class="px-2.5 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600 border border-blue-500/20 text-blue-400 hover:text-white text-[10px] font-bold transition">
                        + Tambah Item
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="space-y-2 bg-slate-950/50 p-3 rounded-xl border border-slate-800/80">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-bold text-slate-500" x-text="`Item #${index + 1}`"></span>
                                <button type="button" @click="removeItem(index)" class="text-[10px] font-bold text-red-400 hover:text-red-300">
                                    Hapus
                                </button>
                            </div>
                            
                            <!-- Item name input -->
                            <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" required placeholder="Nama Barang" 
                                   class="w-full px-3 py-1.5 text-xs rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                            
                            <div class="grid grid-cols-3 gap-2">
                                <!-- Item Category -->
                                <div>
                                    <label class="block text-[9px] text-slate-500 font-bold mb-0.5">Kategori</label>
                                    <select :name="`items[${index}][category]`" x-model="item.category"
                                            class="w-full px-1.5 py-1.5 text-[10px] rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                                        <option value="Food">🍔 Food</option>
                                        <option value="Beverage">🥤 Bev</option>
                                        <option value="Snack">🍿 Snack</option>
                                        <option value="Household">🧹 House</option>
                                        <option value="Personal Care">🧴 Care</option>
                                        <option value="Electronics">🔌 Elect</option>
                                        <option value="Clothing">👕 Cloth</option>
                                        <option value="Medicine">💊 Med</option>
                                        <option value="Others">📦 Other</option>
                                    </select>
                                </div>
                                <!-- Qty -->
                                <div>
                                    <label class="block text-[9px] text-slate-500 font-bold mb-0.5">Jumlah</label>
                                    <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" required min="1" 
                                           class="w-full px-2 py-1.5 text-xs text-center rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                                </div>
                                <!-- Price -->
                                <div>
                                    <label class="block text-[9px] text-slate-500 font-bold mb-0.5">Harga</label>
                                    <input type="number" :name="`items[${index}][price]`" x-model.number="item.price" required 
                                           class="w-full px-2 py-1.5 text-xs rounded-lg bg-slate-950 border border-slate-800 text-white focus:border-blue-500 focus:outline-none transition">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Mathematical calculation aggregates -->
            <div class="glass-card rounded-2xl p-5 flex flex-col gap-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Total Terhitung:</span>
                    <span class="font-bold text-slate-300">Rp <span x-text="calculateSubtotalSum().toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between items-center border-t border-slate-800 pt-3">
                    <span class="text-xs text-slate-400">Total Disimpan:</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black text-cyan-400">Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span></span>
                        <button type="button" @click="totalPrice = calculateSubtotalSum()" class="p-1 rounded bg-slate-850 hover:bg-slate-800 text-slate-400 hover:text-white" title="Gunakan total terhitung">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18" /></svg>
                        </button>
                    </div>
                </div>
                <!-- Keep hidden input to submit final total price -->
                <input type="hidden" name="total_price" :value="totalPrice">
            </div>

            <!-- Submit action buttons -->
            <div class="flex flex-col gap-2.5">
                <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 transition shadow-lg shadow-blue-500/10">
                    Verifikasi & Simpan Perubahan
                </button>
                <button type="button" onclick="closeWebApp()" class="w-full py-3 rounded-xl text-xs font-bold text-slate-400 bg-slate-900 border border-slate-800 hover:bg-slate-800 transition">
                    Batalkan
                </button>
            </div>
        </form>

    </div>

    <script>
        // Initialize Telegram WebApp
        Telegram.WebApp.ready();
        Telegram.WebApp.expand();

        function closeWebApp() {
            Telegram.WebApp.close();
        }

        function telegramEditModel() {
            return {
                tab: 'form',
                storeName: @json($receipt->store_name),
                receiptDate: @json($receipt->receipt_date),
                totalPrice: @json((float)$receipt->total_price),
                tax: @json((float)($receipt->tax ?? 0)),
                discount: @json((float)($receipt->discount ?? 0)),
                category: @json($receipt->category ?? 'Others'),
                confidenceScore: @json($receipt->confidence_score !== null ? (int)$receipt->confidence_score : null),
                zoomScale: 1,
                items: @json(
                    $receipt->items->map(function($item) {
                        return [
                            'item_name' => $item->item_name,
                            'category' => $item->category ?? 'Others',
                            'price' => (float)$item->price,
                            'qty' => (int)$item->qty
                        ];
                    })
                ),

                addItem() {
                    this.items.push({
                        item_name: '',
                        category: 'Others',
                        price: 0,
                        qty: 1
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.totalPrice = this.calculateSubtotalSum();
                },

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
</body>
</html>
