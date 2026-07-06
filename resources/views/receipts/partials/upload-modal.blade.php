<!-- Upload Receipt Modal (Alpine.js controlled) -->
<div x-data="{ 
        open: false, 
        mode: 'upload', // 'upload' or 'camera'
        isDragging: false, 
        isUploading: false,
        fileName: '',
        filePreview: null,
        stream: null,
        cameraError: '',
        cameraFacingMode: 'environment',
        
        
        init() {
            this.$watch('open', value => {
                if (!value) {
                    this.stopCamera();
                    this.mode = 'upload';
                    this.filePreview = null;
                    this.fileName = '';
                    if(this.$refs.fileInput) this.$refs.fileInput.value = '';
                }
            });
            this.$watch('mode', value => {
                if (value === 'camera') {
                    this.filePreview = null;
                    this.fileName = '';
                    if(this.$refs.fileInput) this.$refs.fileInput.value = '';
                    this.startCamera();
                } else {
                    this.stopCamera();
                }
            });
        },
        
        async startCamera() {
            this.cameraError = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: { exact: this.cameraFacingMode } } 
                });
                if (this.$refs.video) {
                    this.$refs.video.srcObject = this.stream;
                }
            } catch (err) {
                try {
                    // Fallback to basic facingMode if exact is not supported (desktop/some phones)
                    this.stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: this.cameraFacingMode } 
                    });
                    if (this.$refs.video) {
                        this.$refs.video.srcObject = this.stream;
                    }
                } catch (e2) {
                    this.cameraError = 'Tidak dapat mengakses kamera. Pastikan izin diberikan.';
                    console.error(e2);
                }
            }
        },
        
        flipCamera() {
            this.cameraFacingMode = this.cameraFacingMode === 'environment' ? 'user' : 'environment';
            this.stopCamera();
            this.startCamera();
        },
        
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },
        
        takePhoto() {
            if (!this.$refs.video) return;
            const canvas = document.createElement('canvas');
            canvas.width = this.$refs.video.videoWidth;
            canvas.height = this.$refs.video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this.$refs.video, 0, 0, canvas.width, canvas.height);
            
            canvas.toBlob((blob) => {
                const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
                this.processFile(file);
                this.mode = 'upload'; // switch back to show preview
            }, 'image/jpeg', 0.9);
        },
        
        handleDrop(e) {
            this.isDragging = false;
            if (e.dataTransfer.files.length > 0) {
                this.processFile(e.dataTransfer.files[0]);
            }
        },
        
        handleFileSelect(e) {
            if (e.target.files.length > 0) {
                this.processFile(e.target.files[0]);
            }
        },
        
        processFile(file) {
            if (!file.type.match('image.*')) {
                alert('Pilih file gambar (JPG, PNG)!');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran maksimal file 5MB!');
                return;
            }
            
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => this.filePreview = e.target.result;
            reader.readAsDataURL(file);
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.$refs.fileInput.files = dataTransfer.files;
        },
        
        submitForm() {
            if (!this.$refs.fileInput.files.length) return;
            this.isUploading = true;
            this.$refs.uploadForm.submit();
        }
    }" @open-upload-modal.window="open = true" @set-upload-mode.window="mode = $event.detail"
    @keydown.escape.window="open = false" class="relative z-50" aria-labelledby="modal-title" role="dialog"
    aria-modal="true" x-cloak>

    <!-- Backdrop -->
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Panel -->
    <div x-show="open" class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" @click.away="!isUploading && (open = false)" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#0a0f1e] border border-slate-200 dark:border-white/10 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">

                <form x-ref="uploadForm" action="{{ route('receipts.upload') }}" method="POST"
                    enctype="multipart/form-data" @submit.prevent="submitForm">
                    @csrf

                    <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4 relative">
                        <!-- Loading Overlay -->
                        <div x-show="isUploading"
                            class="absolute inset-0 z-50 bg-white/95 dark:bg-[#0a0f1e]/95 backdrop-blur-sm flex flex-col items-center justify-center rounded-2xl p-6">
                            <!-- Image Container with Laser Effect -->
                            <div
                                class="relative w-40 h-56 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10 shadow-md mb-6 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                <template x-if="filePreview">
                                    <img :src="filePreview"
                                        class="w-full h-full object-cover opacity-60 filter blur-[0.5px]">
                                </template>
                                <template x-if="!filePreview">
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800 text-slate-400">
                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                </template>
                                <!-- Laser Line -->
                                <div
                                    class="absolute left-0 right-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent shadow-[0_0_8px_#3b82f6] animate-scanner-laser">
                                </div>
                            </div>

                            <svg class="animate-spin h-8 w-8 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-bold dark:text-white text-slate-900 font-display">Memproses dengan
                                AI OCR...</h3>

                            <!-- Dynamic Status Text -->
                            <div x-data="{ 
                                statuses: ['Memulai AI OCR...', 'Membaca gambar struk...', 'Menganalisis teks struk...', 'Mengekstrak harga & nominal...', 'Mengkategorikan item...', 'Menyimpan transaksi...'],
                                currentIdx: 0,
                                timer: null,
                                init() {
                                    this.timer = setInterval(() => {
                                        this.currentIdx = (this.currentIdx + 1) % this.statuses.length;
                                    }, 2000);
                                },
                                destroy() {
                                    if (this.timer) clearInterval(this.timer);
                                }
                            }" class="text-sm font-medium text-blue-600 dark:text-blue-400 mt-2 text-center h-5">
                                <span x-text="statuses[currentIdx]"></span>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-3 text-center px-6">Mohon jangan
                                menutup halaman ini.</p>
                        </div>

                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white font-display"
                                    id="modal-title">Pindai atau Unggah Struk</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">Pilih mode untuk memasukkan foto struk belanja
                                        Anda.</p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 flex flex-row gap-3 mb-4">
                                    <button type="button" @click="mode = 'upload'"
                                        :class="mode === 'upload' ? 'ring-2 ring-blue-600' : 'ring-1 ring-slate-200 dark:ring-slate-700'"
                                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-slate-800 py-3 text-sm font-semibold text-slate-900 dark:text-white transition-all hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm">
                                        <svg class="w-5 h-5 text-slate-800 dark:text-white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        Upload File
                                    </button>
                                    <button type="button" @click="mode = 'camera'"
                                        :class="mode === 'camera' ? 'ring-2 ring-blue-700 bg-blue-700' : 'bg-blue-600'"
                                        class="flex-1 flex items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white transition-all hover:bg-blue-700 shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                        </svg>
                                        Kamera
                                    </button>
                                </div>

                                <!-- Camera Scanner Zone -->
                                <div x-show="mode === 'camera'"
                                    class="mt-4 rounded-xl border border-slate-300 dark:border-white/20 overflow-hidden bg-black relative min-h-[300px] flex items-center justify-center">
                                    <div x-show="cameraError" class="p-4 text-center text-red-500 text-sm"
                                        x-text="cameraError"></div>
                                    <video x-ref="video" class="w-full h-full object-cover" autoplay playsinline
                                        x-show="!cameraError && stream"></video>
                                    <div x-show="!cameraError && !stream" class="text-slate-400 text-sm">Membuka
                                        kamera...</div>

                                    <!-- Camera Controls overlay -->
                                    <div x-show="stream"
                                        class="absolute bottom-4 left-0 right-0 flex justify-center items-center">
                                        <button type="button" @click="takePhoto"
                                            class="bg-white text-blue-600 rounded-full p-3 shadow-lg shadow-black/50 hover:bg-slate-100 transition-colors z-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-8 h-8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" title="Putar Kamera Belakang/Depan" @click="flipCamera"
                                            class="absolute right-4 bottom-2 bg-slate-900/60 backdrop-blur-md text-white rounded-full p-2 border border-white/20 hover:bg-slate-800/80 transition-colors z-10 transition-transform active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Drag & Drop Zone -->
                                <div x-show="mode === 'upload'"
                                    class="mt-4 flex justify-center rounded-xl border border-dashed px-6 py-10 transition-colors"
                                    :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-slate-300 dark:border-white/20'"
                                    @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleDrop">

                                    <div class="text-center">
                                        <!-- Show SVG if no preview -->
                                        <svg x-show="!filePreview"
                                            class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600"
                                            viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                                                clip-rule="evenodd" />
                                        </svg>

                                        <!-- Show Image Preview -->
                                        <div x-show="filePreview"
                                            class="mx-auto w-32 h-32 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10 shadow-sm mb-3 relative group"
                                            style="display: none;">
                                            <img :src="filePreview" class="w-full h-full object-cover" alt="Preview">
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button type="button"
                                                    @click.stop="filePreview = null; fileName = ''; $refs.fileInput.value = ''"
                                                    class="text-white bg-red-500 rounded-full p-1.5 hover:bg-red-600">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-4 flex text-sm leading-6 text-slate-600 dark:text-slate-400 justify-center">
                                            <label for="file-upload"
                                                class="relative cursor-pointer rounded-md font-semibold text-blue-600 dark:text-blue-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                                <span
                                                    x-text="fileName ? 'Pilih gambar lain' : 'Upload file gambar'"></span>
                                                <input id="file-upload" x-ref="fileInput" name="receipt_image"
                                                    type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                                                    class="sr-only" @change="handleFileSelect">
                                            </label>
                                            <p class="pl-1" x-show="!fileName">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs leading-5 text-slate-500 dark:text-slate-500"
                                            x-text="fileName || 'PNG, JPG up to 5MB'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/5 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" :disabled="!fileName || isUploading"
                            class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            Proses & Ekstrak Data
                        </button>
                        <button type="button" @click="open = false" :disabled="isUploading"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-transparent px-3 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-white/10 hover:bg-slate-50 dark:hover:bg-white/5 sm:mt-0 sm:w-auto transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>