@extends('layouts.app')

@section('page-title', 'Scan QR Absensi')

@section('content')
    <div class="fade-in">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Presensi QR</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Arahkan kamera ke QR code untuk mencatat kehadiran</p>
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
                        <h3 class="text-xl font-bold mb-2">Siap untuk Absen?</h3>
                        <p class="text-sm text-slate-300 mb-8 text-center max-w-xs">Pastikan Anda berada di lokasi sekolah dan wajah terlihat jelas.</p>
                        
                        <button type="button" onclick="startAttendance()" class="px-8 py-3.5 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all flex items-center gap-2">
                            <i data-lucide="power" class="w-5 h-5"></i>
                            Mulai Absen Sekarang
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
                    <div id="scanning-overlay" class="hidden absolute inset-0 pointer-events-none flex items-center justify-center z-10">
                        <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent shadow-[0_0_15px_rgba(250,204,21,0.6)] animate-scan"></div>
                    </div>

                    <!-- No Camera Fallback -->
                    <div id="no-camera" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-900/95 text-white backdrop-blur-sm">
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
                <div id="result-container" class="hidden flex-1 h-full">
                    <!-- Success Result -->
                    <div id="scan-result" class="h-full transform transition-all duration-500 translate-y-0 opacity-100 flex flex-col justify-center">
                        <div class="p-8 sm:p-10 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-[2rem] border border-green-200/60 dark:border-green-800/60 shadow-lg shadow-green-100/50 dark:shadow-none h-full flex flex-col justify-center items-center text-center">
                            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-green-500/30 mb-6">
                                <i data-lucide="check" class="w-10 h-10 text-white"></i>
                            </div>
                            <div class="w-full max-w-md">
                                <h4 class="text-2xl font-bold text-green-800 dark:text-green-300">QR Code Berhasil Dipindai!</h4>
                                <div id="qr-data" class="mt-6 bg-white/60 dark:bg-black/20 rounded-2xl p-6 border border-green-100 dark:border-green-800/50 text-left">
                                    <!-- Data will be injected here -->
                                </div>
                                
                                <!-- Submit Form -->
                                <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST" class="hidden mt-8">
                                    @csrf
                                    <input type="hidden" name="qr_data" id="qr-data-input">
                                    <input type="hidden" name="latitude" id="latitude-input">
                                    <input type="hidden" name="longitude" id="longitude-input">

                                    <button type="submit" class="w-full px-8 py-4 bg-navy-800 hover:bg-navy-900 dark:bg-gold-500 dark:hover:bg-gold-600 dark:text-navy-900 text-white rounded-xl text-lg font-bold transition-all shadow-xl hover:shadow-2xl flex items-center justify-center gap-2 group">
                                        <span>Konfirmasi Kehadiran</span>
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

            <!-- Right: Location & Guide (lg:col-span-5) -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">
                
                <!-- Premium Location Card -->
                <div class="bg-white dark:bg-slate-800/80 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-10 dark:opacity-5 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                        <i data-lucide="map" class="w-32 h-32 text-blue-500 -mt-10 -mr-10 rotate-12"></i>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-100 dark:border-blue-500/20">
                                <i data-lucide="map-pin" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-navy-800 dark:text-white leading-tight">Lokasi Saat Ini</h3>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span id="location-status-dot" class="w-1.5 h-1.5 bg-yellow-400 rounded-full animate-pulse"></span>
                                    <span id="location-status" class="text-[10px] font-medium text-yellow-600 dark:text-yellow-400">Mendeteksi...</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl mb-4 border border-slate-100 dark:border-slate-800">
                            <p id="location-name" class="text-sm font-semibold text-slate-800 dark:text-slate-200 line-clamp-2">Mengambil koordinat lokasi...</p>
                            
                            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700/50">
                                <div>
                                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold mb-1">Latitude</p>
                                    <p id="latitude-display" class="text-xs font-mono font-medium text-slate-700 dark:text-slate-300">-</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold mb-1">Longitude</p>
                                    <p id="longitude-display" class="text-xs font-mono font-medium text-slate-700 dark:text-slate-300">-</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                <i data-lucide="crosshair" class="w-3.5 h-3.5"></i>
                                <span>Akurasi: <strong id="accuracy-display" class="text-slate-700 dark:text-slate-300">-</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span id="last-update">--:--</span>
                            </div>
                        </div>
                    </div>
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
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Arahkan Kamera</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Posisikan QR code agar berada di tengah layar kamera.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full bg-navy-800 dark:bg-gold-500 text-white dark:text-navy-900 flex items-center justify-center text-xs font-bold shrink-0">2</div>
                                <div class="w-px h-full bg-slate-200 dark:bg-slate-700 my-1"></div>
                            </div>
                            <div class="pb-2">
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Tunggu Pemindaian</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Sistem akan otomatis mendeteksi data QR dengan cepat.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full bg-navy-800 dark:bg-gold-500 text-white dark:text-navy-900 flex items-center justify-center text-xs font-bold shrink-0">3</div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-navy-800 dark:text-slate-200">Konfirmasi Kehadiran</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Klik tombol konfirmasi setelah data profil Anda muncul.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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
        const scanError = document.getElementById('scan-error');
        const errorMessage = document.getElementById('error-message');
        const noCamera = document.getElementById('no-camera');

        // Location elements
        const locationStatus = document.getElementById('location-status');
        const locationName = document.getElementById('location-name');
        const latitudeDisplay = document.getElementById('latitude-display');
        const longitudeDisplay = document.getElementById('longitude-display');
        const accuracyDisplay = document.getElementById('accuracy-display');
        const lastUpdate = document.getElementById('last-update');

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
                document.getElementById('scanning-overlay').classList.remove('hidden');
                startScanning();
            } catch (err) {
                console.error('Camera error:', err);
                noCamera.classList.remove('hidden');
                document.getElementById('scanning-overlay').classList.add('hidden');
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
                
                // Mencegah HP lag/freeze dengan memberi jeda 150ms antar scan (sekitar 6-7 FPS)
                // Ini bikin video tetap lancar dan scan malah terasa lebih 'sat set' karena CPU tidak terbebani
                setTimeout(scanFrame, 150);
            }
            scanFrame();
        }

        // Handle QR success
        function handleQRSuccess(data) {
            stopCamera();
            document.getElementById('scanning-overlay').classList.add('hidden');
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
                // If not JSON, assume it's the raw ID (for backward compatibility)
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
            
            // Fetch latest data from server to ensure email & subject are always present (even if QR is old)
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
                    
                    attendanceForm.classList.remove('hidden');
                    getLocation();
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

        // Get location
        function getLocation() {
            const statusDot = document.getElementById('location-status-dot');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;

                        // Update displays
                        latitudeDisplay.textContent = lat.toFixed(6);
                        longitudeDisplay.textContent = lng.toFixed(6);
                        accuracyDisplay.textContent = Math.round(accuracy) + ' meter';

                        // Update status text
                        locationStatus.textContent = 'Akurat';
                        locationStatus.className = 'text-[10px] font-medium text-green-600 dark:text-green-400';
                        
                        // Update status dot
                        if(statusDot) statusDot.className = 'w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse';
                        
                        locationName.textContent = 'Koordinat ditemukan';

                        // Update time
                        const now = new Date();
                        lastUpdate.textContent = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

                        // Save to form
                        document.getElementById('latitude-input').value = lat;
                        document.getElementById('longitude-input').value = lng;

                        // Reverse geocode (optional)
                        reverseGeocode(lat, lng);
                    },
                    (error) => {
                        locationStatus.textContent = 'Gagal';
                        locationStatus.className = 'text-[10px] font-medium text-red-600 dark:text-red-400';
                        
                        if(statusDot) statusDot.className = 'w-1.5 h-1.5 bg-red-500 rounded-full';
                        
                        locationName.textContent = 'Lokasi tidak tersedia';
                        console.warn('Location error:', error);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                locationStatus.textContent = 'Tidak didukung';
                locationStatus.className = 'text-[10px] font-medium text-slate-500 dark:text-slate-400';
                if(statusDot) statusDot.className = 'w-1.5 h-1.5 bg-slate-500 rounded-full';
                locationName.textContent = 'Browser tidak support geolocation';
            }
        }

        // Reverse geocode (get address from coordinates)
        async function reverseGeocode(lat, lng) {
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&accept-language=id`
                );
                const data = await response.json();

                if (data && data.address) {
                    let location = '';
                    if (data.address.city) location = data.address.city;
                    else if (data.address.town) location = data.address.town;
                    else if (data.address.village) location = data.address.village;

                    locationName.textContent = location || 'Lokasi ditemukan';
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            }
        }

        // Cleanup
        window.addEventListener('beforeunload', () => {
            stopCamera();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            // initCamera() is now called via the "Mulai Absen" button
            getLocation();
        });
    </script>

    <style>
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
    </style>
@endsection