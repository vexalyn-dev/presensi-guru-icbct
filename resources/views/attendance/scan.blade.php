@extends('layouts.app')

@section('page-title', 'Scan QR Absensi')

@section('content')
    <div id="attendance-root" class="fade-in" x-data="{ mode: 'masuk' }" x-init="$watch('mode', val => {
        const hwInput = document.getElementById('hardware-mode-input');
        const attInput = document.getElementById('attendance-mode-input');
        if (hwInput) hwInput.value = val;
        if (attInput) attInput.value = val;
    })">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="scan-line" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Presensi QR</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pilih mode presensi dan arahkan kamera ke QR code</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm group w-fit">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

            <!-- Left: Camera & Upload (lg:col-span-8) -->
            <div class="lg:col-span-7 xl:col-span-8 flex flex-col">
                <!-- Premium Camera Container -->
                <div id="camera-box" class="relative rounded-[2rem] overflow-hidden bg-slate-900 border-[6px] border-white dark:border-slate-800 shadow-2xl shadow-slate-200/50 dark:shadow-slate-900/50 aspect-[4/3] sm:aspect-[16/10] lg:aspect-auto flex-1 transition-all duration-500">
                    <!-- Camera Video Element -->
                    <video id="camera-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline></video>
                    
                    <!-- Camera Idle Overlay -->
                    <div id="camera-idle-overlay" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-md text-white transition-opacity duration-500">
                        <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mb-6 border border-white/20 shadow-[0_0_30px_rgba(255,255,255,0.1)]">
                            <i data-lucide="scan" class="w-10 h-10 text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2" x-text="mode === 'masuk' ? 'Presensi Masuk' : 'Presensi Keluar'"></h3>
                        <p class="text-sm text-slate-300 mb-8 text-center max-w-xs">Pastikan Anda berada di lokasi sekolah dan wajah terlihat jelas.</p>
                        
                        <button type="button" onclick="startAttendance()" class="px-8 py-3.5 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all flex items-center gap-2">
                            <i data-lucide="power" class="w-5 h-5"></i>
                            Mulai Presensi Sekarang
                        </button>
                    </div>

                    <!-- Decorative Corners -->
                    <div class="absolute inset-0 pointer-events-none p-6 flex flex-col justify-between opacity-50 z-10">
                        <div class="flex justify-between">
                            <div class="w-12 h-12 border-t-4 border-l-4 border-white/80 rounded-tl-2xl"></div>
                            <div class="w-12 h-12 border-t-4 border-r-4 border-white/80 rounded-tr-2xl"></div>
                        </div>
                        <div class="flex justify-between">
                            <div class="w-12 h-12 border-b-4 border-l-4 border-white/80 rounded-bl-2xl"></div>
                            <div class="w-12 h-12 border-b-4 border-r-4 border-white/80 rounded-br-2xl"></div>
                        </div>
                    </div>

                    <!-- Overlay Controls (Gallery) -->
                    <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
                        <button type="button" onclick="document.getElementById('gallery-input').click()" 
                                class="flex items-center gap-2 px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white rounded-xl transition-all shadow-lg border border-white/30 group">
                            <i data-lucide="image" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium hidden sm:inline-block">Unggah Gambar</span>
                        </button>
                        <input type="file" id="gallery-input" accept="image/*" class="hidden" onchange="handleGalleryUpload(this)">
                    </div>

                    <!-- Scanning Indicator Overlay -->
                    <div id="scanning-overlay" class="absolute inset-0 pointer-events-none z-10 hidden">
                        <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent shadow-[0_0_15px_rgba(250,204,21,0.6)] animate-scan"></div>
                    </div>

                    <!-- No Camera Fallback -->
                    <div id="no-camera" class="absolute inset-0 z-10 hidden bg-slate-900/95 text-white backdrop-blur-sm">
                        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4 border border-slate-700">
                            <i data-lucide="camera-off" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <p class="text-base font-medium">Kamera tidak tersedia</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-[250px] text-center">Pastikan Anda memberikan izin akses kamera ke browser.</p>
                    </div>
                </div>

                <!-- Hidden Canvas -->
                <canvas id="qr-canvas" class="hidden"></canvas>

                <!-- Scan Results area -->
                <div id="result-container" class="hidden h-full">
                    <!-- Success Result -->
                    <div id="scan-result" class="h-full transform transition-all duration-500 translate-y-0 opacity-100 flex flex-col justify-center">
                        <div class="p-8 sm:p-10 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-[2rem] border border-green-200/60 dark:border-green-800/60 shadow-lg shadow-green-100/50 dark:shadow-none h-full flex flex-col justify-center items-center text-center">
                            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-green-500/30 mb-6">
                                <i data-lucide="check" id="success-icon-type" class="w-10 h-10 text-white"></i>
                            </div>
                            <div class="w-full max-w-md">
                                <h4 class="text-2xl font-bold text-green-800 dark:text-green-300" id="success-title">QR Code Berhasil Dipindai!</h4>
                                <div id="qr-data" class="mt-6 bg-white/60 dark:bg-black/20 rounded-2xl p-6 border border-green-100 dark:border-green-800/50 text-left">
                                    <!-- Data will be injected here -->
                                </div>
                                
                                <!-- Submit Form -->
                                <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST" class="hidden mt-8">
                                    @csrf
                                    <input type="hidden" name="qr_data" id="qr-data-input">
                                    <input type="hidden" name="latitude" id="latitude-input">
                                    <input type="hidden" name="longitude" id="longitude-input">
                                    <input type="hidden" name="mode" id="attendance-mode-input" value="masuk">

                                    <button type="submit" id="btn-confirm-attendance" class="w-full px-8 py-4 bg-navy-800 hover:bg-navy-900 dark:bg-gold-500 dark:hover:bg-gold-600 dark:text-navy-900 text-white rounded-xl text-lg font-bold transition-all shadow-xl hover:shadow-2xl flex items-center justify-center gap-2 group">
                                        <span id="btn-confirm-text">Konfirmasi Presensi Masuk</span>
                                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Alert -->
                <div id="scan-error" class="hidden transform transition-all duration-300 mt-4">
                    <div class="p-4 sm:p-5 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800/60 flex items-start gap-3 shadow-sm">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Gagal Memindai</h4>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1" id="error-message">Terjadi kesalahan saat memindai QR.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Mode Card & Guide (lg:col-span-5) -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">
                
                <!-- ✅ MODE CARD - Match "Cara Presensi" Card Style -->
                <div class="bg-gradient-to-b from-slate-50 to-white dark:from-slate-800/40 dark:to-slate-800/20 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/60">
                    <div class="relative z-10">
                        <!-- Header -->
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-sm">
                                <i data-lucide="toggle-left" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-navy-800 dark:text-white leading-tight">Mode Presensi</h3>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Pilih sesuai kebutuhan</p>
                            </div>
                        </div>

                        <!-- Mode Buttons -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <!-- Mode Masuk -->
                            <button @click="mode = 'masuk'" 
                                    :class="mode === 'masuk' ? 'bg-gradient-to-br from-gold-400 to-gold-500 text-white ring-2 ring-gold-500 shadow-md' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                    class="relative overflow-hidden rounded-2xl p-4 transition-all duration-300 group">
                                
                                <!-- Icon -->
                                <div class="mb-2">
                                    <div :class="mode === 'masuk' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-600'" 
                                         class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto transition-all duration-300 group-hover:scale-110">
                                        <i data-lucide="log-in" class="w-5 h-5" :class="mode === 'masuk' ? 'text-white' : 'text-slate-600 dark:text-slate-400'"></i>
                                    </div>
                                </div>
                                
                                <!-- Text -->
                                <p class="text-sm font-bold">Masuk</p>
                                <p class="text-[9px] opacity-80 mt-0.5">07:00 - 08:00</p>
                                
                                <!-- Active Indicator -->
                                <div x-show="mode === 'masuk'" 
                                     class="absolute top-2 right-2 w-5 h-5 bg-white rounded-full flex items-center justify-center shadow-md animate-scale-in">
                                    <i data-lucide="check" class="w-3 h-3 text-gold-500"></i>
                                </div>
                            </button>

                            <!-- Mode Keluar -->
                            <button @click="mode = 'keluar'" 
                                    :class="mode === 'keluar' ? 'bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white ring-2 ring-navy-800 dark:ring-gold-500 shadow-md' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                    class="relative overflow-hidden rounded-2xl p-4 transition-all duration-300 group">
                                
                                <!-- Icon -->
                                <div class="mb-2">
                                    <div :class="mode === 'keluar' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-600'" 
                                         class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto transition-all duration-300 group-hover:scale-110">
                                        <i data-lucide="log-out" class="w-5 h-5" :class="mode === 'keluar' ? 'text-white' : 'text-slate-600 dark:text-slate-400'"></i>
                                    </div>
                                </div>
                                
                                <!-- Text -->
                                <p class="text-sm font-bold">Keluar</p>
                                <p class="text-[9px] opacity-80 mt-0.5">≥ 15:00</p>
                                
                                <!-- Active Indicator -->
                                <div x-show="mode === 'keluar'" 
                                     class="absolute top-2 right-2 w-5 h-5 bg-white rounded-full flex items-center justify-center shadow-md animate-scale-in">
                                    <i data-lucide="check" class="w-3 h-3 text-gold-500 dark:text-navy-900"></i>
                                </div>
                            </button>
                        </div>

                        <!-- Info Box -->
                        <div class="p-3 bg-slate-100 dark:bg-slate-900/50 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                            <p x-show="mode === 'masuk'" class="text-xs text-green-600 dark:text-green-400 font-medium text-center animate-fade-in">
                                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                                Untuk presensi kedatangan pagi
                            </p>
                            <p x-show="mode === 'keluar'" class="text-xs text-blue-600 dark:text-blue-400 font-medium text-center animate-fade-in">
                                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                                Untuk presensi kepulangan sore
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ✅ CARD KHUSUS HARDWARE SCANNER / INPUT MANUAL (DENGAN MODE) -->
                <div class="card p-5 mt-6 group hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <i data-lucide="scan-barcode" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Scan via Alat / Manual</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan hardware scanner atau ketik kode</p>
                        </div>
                    </div>

                    <!-- Mode Toggle (Masuk / Keluar) -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <!-- Mode Masuk -->
                        <button @click="mode = 'masuk'; $nextTick(() => document.getElementById('hardware-qr-input').focus())" 
                                :class="mode === 'masuk' ? 'bg-gradient-to-br from-green-500 to-emerald-600 text-white ring-2 ring-green-500 shadow-md scale-105' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                class="relative overflow-hidden rounded-xl p-3 transition-all duration-300 flex items-center justify-center gap-2">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span class="text-sm font-bold">Masuk</span>
                            <div x-show="mode === 'masuk'" 
                                 class="absolute top-2 right-2 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm animate-scale-in">
                                <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                            </div>
                        </button>

                        <!-- Mode Keluar -->
                        <button @click="mode = 'keluar'; $nextTick(() => document.getElementById('hardware-qr-input').focus())" 
                                :class="mode === 'keluar' ? 'bg-gradient-to-br from-blue-500 to-cyan-600 text-white ring-2 ring-blue-500 shadow-md scale-105' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                class="relative overflow-hidden rounded-xl p-3 transition-all duration-300 flex items-center justify-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="text-sm font-bold">Keluar</span>
                            <div x-show="mode === 'keluar'" 
                                 class="absolute top-2 right-2 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm animate-scale-in">
                                <i data-lucide="check" class="w-3 h-3 text-blue-600"></i>
                            </div>
                        </button>
                    </div>

                    <!-- Form Input Scanner -->
                    <form id="hardware-scanner-form">
                        @csrf
                        <input type="hidden" name="mode" id="hardware-mode-input" value="masuk" />
                        <div class="relative">
                            <i data-lucide="scan-line" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                            <input type="text" id="hardware-qr-input" name="qr_data"
                                   class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all"
                                   placeholder="Klik di sini, lalu scan QR code dengan alat..."
                                   @blur="$nextTick(() => { if (!document.activeElement.closest('.card')) $el.focus(); })"
                                   autofocus>

                            <input type="hidden" name="latitude" id="hardware-latitude" value="">
                            <input type="hidden" name="longitude" id="hardware-longitude" value="">
                        </div>

                        <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Alat akan otomatis menekan Enter setelah scan.
                        </p>
                    </form>
                </div>

                <!-- Modern Guide / Stepper -->
                <div class="bg-gradient-to-b from-slate-50 to-white dark:from-slate-800/40 dark:to-slate-800/20 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 mb-6">
                        <i data-lucide="help-circle" class="w-5 h-5 text-gold-500"></i>
                        <h3 class="text-sm font-bold text-navy-800 dark:text-white uppercase tracking-wide">Cara Presensi</h3>
                    </div>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full bg-navy-800 dark:bg-gold-500 text-white dark:text-navy-900 flex items-center justify-center text-xs font-bold shrink-0">1</div>
                                <div class="w-px h-full bg-slate-200 dark:bg-slate-700 my-1"></div>
                            </div>
                            <div class="pb-2">
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Pilih Mode</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pilih mode Masuk atau Keluar sesuai kebutuhan.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full bg-navy-800 dark:bg-gold-500 text-white dark:text-navy-900 flex items-center justify-center text-xs font-bold shrink-0">2</div>
                                <div class="w-px h-full bg-slate-200 dark:bg-slate-700 my-1"></div>
                            </div>
                            <div class="pb-2">
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Arahkan Kamera</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Posisikan QR code agar berada di tengah layar kamera.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full bg-navy-800 dark:bg-gold-500 text-white dark:text-navy-900 flex items-center justify-center text-xs font-bold shrink-0">3</div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Konfirmasi</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Klik tombol konfirmasi setelah data profil Anda muncul.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-[9999] space-y-3 pointer-events-none"></div>

    <!-- Include jsQR library -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <script>
        // Global variables
        let video = document.getElementById('camera-video');
        let canvas = document.getElementById('qr-canvas');
        let ctx = canvas.getContext('2d');
        let stream = null;
        let scanning = false;

        // DOM Elements
        const scanResult = document.getElementById('scan-result');
        const qrDataEl = document.getElementById('qr-data');
        const qrDataInput = document.getElementById('qr-data-input');
        const attendanceForm = document.getElementById('attendance-form');
        // Add hidden mode input to attendance form if not present
        if (!document.getElementById('attendance-mode-input')) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'mode';
            hidden.id = 'attendance-mode-input';
            hidden.value = 'masuk';
            attendanceForm.appendChild(hidden);
        }
        const scanError = document.getElementById('scan-error');
        const errorMessage = document.getElementById('error-message');
        const noCamera = document.getElementById('no-camera');

        // Hardware QR Scanner Logic
        let hardwareScanTimeout = null;

        function getAlpineData(element) {
            if (!element) return null;

            if (element.__x && element.__x.$data) {
                return element.__x.$data;
            }

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try {
                    return window.Alpine.$data(element);
                } catch (e) {
                    return null;
                }
            }

            return null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hardwareInput = document.getElementById('hardware-qr-input');

            if (hardwareInput) {
                hardwareInput.focus();

                // Refocus saat blur, kecuali ke elemen mode-button di dalam card scanner
                hardwareInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        const focused = document.activeElement;
                        const isInsideScannerCard = focused && focused.closest('#hardware-scanner-form') === null
                            && focused.tagName === 'BUTTON'
                            && focused.closest('.card') !== null;
                        if (!isInsideScannerCard) {
                            // Hanya refocus jika bukan klik ke tombol mode scanner
                        }
                    }, 150);
                });

                hardwareInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const qrData = this.value.trim();

                        if (qrData) {
                            if (hardwareScanTimeout) {
                                clearTimeout(hardwareScanTimeout);
                            }

                            hardwareScanTimeout = setTimeout(() => {
                                processHardwareScan(qrData);
                                this.value = '';
                            }, 100);
                        }
                    }
                });
            }

            window.processHardwareScan = function(qrData) {
                // Ambil mode yang dipilih user
                const currentMode = document.getElementById('hardware-mode-input')?.value || 'masuk';

                // Hide hardware input area dan show result container
                document.getElementById('camera-box').classList.add('hidden');
                document.getElementById('result-container').classList.remove('hidden');
                document.getElementById('scan-result').classList.remove('hidden');
                
                let teacherId = null;
                let qrToken = null;
                
                try {
                    const jsonData = JSON.parse(qrData);
                    teacherId = jsonData.teacher_id;
                    qrToken = jsonData.token;
                } catch (e) {
                    teacherId = qrData;
                }

                if (!teacherId) {
                    showError('Format QR Code tidak dikenali.');
                    return;
                }

                // Tampilkan loading state
                const qrDataEl = document.getElementById('qr-data');
                qrDataEl.innerHTML = `
                    <div class="flex items-center justify-center p-4">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-gold-500"></div>
                        <span class="ml-2 text-sm text-slate-500">Memuat data guru...</span>
                    </div>
                `;
                
                // 1. Fetch teacher data
                fetch(`/teachers/${teacherId}/data`)
                    .then(response => response.json())
                    .then(teacherData => {
                        if (teacherData.error) throw new Error(teacherData.error);
                        
                        qrDataEl.innerHTML = `
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Nama Guru</span>
                                    <span class="text-sm font-bold text-navy-800 dark:text-white">${teacherData.name || '-'}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Email</span>
                                    <span class="text-xs text-slate-600 dark:text-slate-400">${teacherData.email || '-'}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Mata Pelajaran</span>
                                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">${teacherData.subject || 'Belum diatur'}</span>
                                </div>
                            </div>
                        `;
                        
                        // Update hidden inputs
                        document.getElementById('qr-data-input').value = JSON.stringify({
                            teacher_id: teacherId,
                            token: qrToken
                        });
                        document.getElementById('attendance-mode-input').value = currentMode;
                        
                        // 2. AUTO CHECK STATUS
                        fetch(`/attendance/check-status/${teacherId}`)
                            .then(res => res.json())
                            .then(statusData => {
                                const alreadyIn = statusData.already_checked_in && !statusData.checked_out;

                                // Gunakan status server ATAU mode yang dipilih user
                                const isKeluar = alreadyIn || currentMode === 'keluar';

                                if (isKeluar && alreadyIn) {
                                    // SUDAH MASUK → TAMPILKAN INFO KELUAR
                                    document.getElementById('success-title').textContent = 'Presensi Keluar';
                                    document.getElementById('success-icon-type').setAttribute('data-lucide', 'log-out');
                                    document.getElementById('btn-confirm-text').textContent = 'Konfirmasi Presensi Keluar';
                                    document.getElementById('attendance-mode-input').value = 'keluar';
                                    
                                    qrDataEl.innerHTML += `
                                        <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Waktu masuk tadi:</p>
                                            <p class="text-sm font-bold text-navy-800 dark:text-white">${statusData.check_in_time} WIB</p>
                                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">Status: ${statusData.status}</p>
                                        </div>
                                    `;
                                } else if (currentMode === 'keluar' && !alreadyIn) {
                                    // Mode keluar tapi belum ada data masuk
                                    document.getElementById('success-title').textContent = 'Peringatan';
                                    document.getElementById('success-icon-type').setAttribute('data-lucide', 'alert-triangle');
                                    document.getElementById('btn-confirm-text').textContent = 'Konfirmasi Presensi Keluar';
                                    document.getElementById('attendance-mode-input').value = 'keluar';
                                    
                                    qrDataEl.innerHTML += `
                                        <div class="mt-4 pt-4 border-t border-yellow-200 dark:border-yellow-800">
                                            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Guru ini belum tercatat presensi masuk hari ini. Presensi keluar tetap akan dicatat.</p>
                                        </div>
                                    `;
                                } else {
                                    // BELUM MASUK → TAMPILKAN INFO MASUK
                                    document.getElementById('success-title').textContent = 'Presensi Masuk';
                                    document.getElementById('success-icon-type').setAttribute('data-lucide', 'log-in');
                                    document.getElementById('btn-confirm-text').textContent = 'Konfirmasi Presensi Masuk';
                                    document.getElementById('attendance-mode-input').value = 'masuk';
                                }

                                document.getElementById('attendance-form').classList.remove('hidden');
                                
                                if (window.lucide) lucide.createIcons();
                            })
                            .catch(err => {
                                console.error('Status check error:', err);
                                document.getElementById('btn-confirm-text').textContent =
                                    currentMode === 'keluar' ? 'Konfirmasi Presensi Keluar' : 'Konfirmasi Presensi Masuk';
                                document.getElementById('attendance-form').classList.remove('hidden');
                            });
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        qrDataEl.textContent = 'Gagal memuat detail guru. Silakan coba lagi.';
                    });
            };
        });

        function startConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const colors = ['#10b981', '#34d399', '#6ee7b7', '#facc15', '#fde047', '#3b82f6', '#60a5fa'];

            for (let i = 0; i < 150; i++) {
                particles.push({
                    x: canvas.width / 2,
                    y: canvas.height / 2,
                    vx: (Math.random() - 0.5) * 15,
                    vy: (Math.random() - 0.5) * 15 - 5,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    size: Math.random() * 8 + 4,
                    rotation: Math.random() * 360,
                    rotationSpeed: (Math.random() - 0.5) * 10,
                    life: 1,
                    decay: Math.random() * 0.02 + 0.01
                });
            }

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let activeParticles = false;

                particles.forEach(p => {
                    if (p.life <= 0) return;

                    activeParticles = true;

                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.3;
                    p.rotation += p.rotationSpeed;
                    p.life -= p.decay;

                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rotation * Math.PI) / 180);
                    ctx.globalAlpha = p.life;
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                    ctx.restore();
                });

                if (activeParticles) {
                    requestAnimationFrame(animate);
                } else {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            }

            animate();
        }

        // Start Attendance explicitly
        function startAttendance() {
            const overlay = document.getElementById('camera-idle-overlay');
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 500);
            initCamera();
        }

        // Initialize camera
        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1920 },
                        height: { ideal: 1080 },
                        advanced: [{ focusMode: "continuous" }]
                    } 
                });
                video.srcObject = stream;
                noCamera.classList.add('hidden');
                noCamera.classList.remove('flex', 'flex-col');
                document.getElementById('scanning-overlay').classList.remove('hidden');
                document.getElementById('scanning-overlay').classList.add('flex', 'items-center', 'justify-center');
                startScanning();
            } catch (err) {
                console.error('Camera error:', err);
                noCamera.classList.remove('hidden');
                noCamera.classList.add('flex', 'flex-col', 'items-center', 'justify-center');
                document.getElementById('scanning-overlay').classList.add('hidden');
                document.getElementById('scanning-overlay').classList.remove('flex', 'items-center', 'justify-center');
                showError('Kamera tidak dapat diakses.');
            }
        }

        // Handle Gallery Upload
        function handleGalleryUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: 'attemptBoth'
                        });
                        
                        if (code) {
                            handleQRSuccess(code.data);
                        } else {
                            showError('QR Code tidak ditemukan dalam gambar ini.');
                        }
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Start scanning
        function startScanning() {
            if (scanning) return;
            scanning = true;

            function scanFrame() {
                if (!scanning || !stream) return;

                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.height = video.videoHeight;
                    canvas.width = video.videoWidth;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'attemptBoth',
                    });

                    if (code) {
                        handleQRSuccess(code.data);
                        scanning = false;
                        return;
                    }
                }
                
                setTimeout(scanFrame, 150);
            }
            scanFrame();
        }

        // Handle QR success
        function handleQRSuccess(data) {
            stopCamera();
            document.getElementById('scanning-overlay').classList.add('hidden');
            document.getElementById('scanning-overlay').classList.remove('flex', 'items-center', 'justify-center');
            document.getElementById('camera-box').classList.add('hidden');
            document.getElementById('result-container').classList.remove('hidden');
            document.getElementById('scan-result').classList.remove('hidden');
            
            let teacherId = null;
            let qrToken = null;
            
            try {
                const jsonData = JSON.parse(data);
                teacherId = jsonData.teacher_id;
                qrToken = jsonData.token;
            } catch (e) {
                teacherId = data;
            }

            if (!teacherId) {
                showError('Format QR Code tidak dikenali.');
                return;
            }

            // Tampilkan loading state
            qrDataEl.innerHTML = `
                <div class="flex items-center justify-center p-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-gold-500"></div>
                    <span class="ml-2 text-sm text-slate-500">Memuat data guru...</span>
                </div>
            `;
            
            // Fetch latest data from server
            fetch(`/teachers/${teacherId}/data`)
                .then(response => response.json())
                .then(teacherData => {
                    if (teacherData.error) throw new Error(teacherData.error);
                    
                    qrDataEl.innerHTML = `
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Nama Guru</span>
                                <span class="text-sm font-bold text-navy-800 dark:text-white">${teacherData.name || '-'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Email</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">${teacherData.email || '-'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase text-slate-400 font-semibold w-24">Mata Pelajaran</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">${teacherData.subject || 'Belum diatur'}</span>
                            </div>
                        </div>
                    `;
                    
                    // Update hidden input for form submission
                    qrDataInput.value = JSON.stringify({
                        teacher_id: teacherId,
                        token: qrToken
                    });
                    
                    // AUTO CHECK STATUS
                    fetch(`/attendance/check-status/${teacherId}`)
                        .then(res => res.json())
                        .then(statusData => {
                            if (statusData.already_checked_in && !statusData.checked_out) {
                                // SUDAH MASUK → TAMPILKAN INFO KELUAR
                                document.getElementById('success-title').textContent = 'Presensi Keluar';
                                document.getElementById('success-icon-type').setAttribute('data-lucide', 'log-out');
                                
                                // Show check-in time
                                qrDataEl.innerHTML += `
                                    <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Waktu masuk tadi:</p>
                                        <p class="text-sm font-bold text-navy-800 dark:text-white">${statusData.check_in_time} WIB</p>
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Status: ${statusData.status}</p>
                                    </div>
                                `;
                                
                                attendanceForm.classList.remove('hidden');
                            } else {
                                // BELUM MASUK → TAMPILKAN INFO MASUK
                                document.getElementById('success-title').textContent = 'Presensi Masuk';
                                document.getElementById('success-icon-type').setAttribute('data-lucide', 'log-in');
                                
                                attendanceForm.classList.remove('hidden');
                            }
                        })
                        .catch(err => {
                            console.error('Status check error:', err);
                            attendanceForm.classList.remove('hidden');
                        });
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    qrDataEl.textContent = 'Gagal memuat detail guru. Silakan coba lagi.';
                });
        }

        // Stop camera
        function stopCamera() {
            scanning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // Show error
        function showError(message) {
            scanError.classList.remove('hidden');
            errorMessage.textContent = message;
            setTimeout(() => {
                scanError.classList.add('hidden');
            }, 5000);
        }

        // Toast Notification Function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const icons = {
                success: 'check-circle',
                error: 'alert-circle',
                warning: 'alert-triangle',
                info: 'info'
            };
            
            const colors = {
                success: 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300',
                error: 'from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300',
                warning: 'from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300',
                info: 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300'
            };
            
            toast.className = `pointer-events-auto flex items-start gap-3 px-5 py-4 bg-gradient-to-r ${colors[type]} border rounded-2xl shadow-2xl backdrop-blur-sm transform transition-all duration-300 translate-x-full opacity-0 max-w-sm`;
            
            toast.innerHTML = `
                <i data-lucide="${icons[type]}" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium leading-relaxed">${message}</p>
                </div>
                <button onclick="this.closest('.pointer-events-auto').remove()" class="flex-shrink-0 ml-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Re-render icons
            if (window.lucide) lucide.createIcons();
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Handle hardware scanner form submit
        const hardwareForm = document.getElementById('hardware-scanner-form');
        if (hardwareForm) {
            hardwareForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const qrData = document.getElementById('hardware-qr-input').value.trim();
                if (qrData) {
                    // hidden mode input is already synced via Alpine $watch, no extra update needed
                    processHardwareScan(qrData);
                    document.getElementById('hardware-qr-input').value = '';
                }
            });
        }

        // Handle attendance form submit (untuk hardware & camera)
        if (attendanceForm) {
            attendanceForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('btn-confirm-attendance');
                if (submitBtn) {
                    // Prevent double clicks but allow native form submission
                    if (submitBtn.hasAttribute('data-submitting')) {
                        e.preventDefault();
                        return;
                    }
                    submitBtn.setAttribute('data-submitting', 'true');
                    
                    setTimeout(() => {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div> Memproses...';
                    }, 10);
                }
            });
        }

        // Cleanup
        window.addEventListener('beforeunload', () => {
            stopCamera();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>

    <style>
        /* Toast Notification Styles */
        #toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            pointer-events: none;
        }

        #toast-container > * {
            pointer-events: auto;
        }

        /* Animation for toast */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scan {
            0%, 100% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        .animate-scan {
            animation: scan 2s linear infinite;
        }
        
        @keyframes scale-in {
            from { 
                opacity: 0; 
                transform: scale(0.5); 
            }
            to { 
                opacity: 1; 
                transform: scale(1); 
            }
        }
        
        .animate-scale-in {
            animation: scale-in 0.3s ease-out forwards;
        }

    </style>
@endsection