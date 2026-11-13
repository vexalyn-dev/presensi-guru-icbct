<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Guru — SMK ICB CT</title>
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes pulse-ring { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.4); opacity: 0; } }

        .fade-in { animation: fadeIn 0.4s ease-out; }
        .animate-scale-in { animation: scaleIn 0.3s ease-out; }
        .animate-pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid currentColor;
            animation: pulse-ring 1.5s ease-out infinite;
        }

        .kiosk-input {
            /* input ini tidak visible tapi tetap menerima keyboard dari barcode gun */
            position: fixed;
            top: -100px;
            left: -100px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-navy-900 to-slate-900 text-white">

{{-- Input tersembunyi — target keyboard dari barcode gun --}}
<input
    type="text"
    id="kiosk-input"
    class="kiosk-input"
    autocomplete="off"
    autofocus
    tabindex="1"
    aria-hidden="true"
>

<div id="app" class="min-h-screen flex flex-col" x-data="kioskApp()" x-init="init()">

    {{-- Header --}}
    <header class="flex items-center justify-between px-8 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 rounded-xl" onerror="this.style.display='none'">
            <div>
                <h1 class="text-lg font-bold tracking-tight">SMK ICB Cinta Teknika</h1>
                <p class="text-xs text-slate-400">Sistem Presensi Guru Digital</p>
            </div>
        </div>
        <div class="flex items-center gap-6 text-sm text-slate-300">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                <span>Siap Scan</span>
            </div>
            <div class="text-right">
                <p class="font-semibold text-white" x-text="currentTime"></p>
                <p class="text-xs text-slate-400" x-text="currentDate"></p>
            </div>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center p-8">

        {{-- State: IDLE — menunggu scan --}}
        <div x-show="state === 'idle'" class="text-center fade-in max-w-sm w-full">
            <div class="relative inline-flex items-center justify-center mb-8">
                <div class="w-28 h-28 rounded-full bg-white/10 flex items-center justify-center border border-white/20">
                    <i data-lucide="scan-barcode" class="w-14 h-14 text-gold-400"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold mb-3">Scan QR Code</h2>
            <p class="text-slate-400 text-base leading-relaxed">Arahkan barcode gun ke QR Code guru.<br>Presensi langsung tercatat otomatis.</p>
            <div class="mt-8 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-sm text-slate-300 border border-white/10">
                <i data-lucide="keyboard" class="w-4 h-4"></i>
                Menunggu input scanner...
            </div>
        </div>

        {{-- State: LOADING — sedang proses --}}
        <div x-show="state === 'loading'" class="text-center fade-in max-w-sm w-full" x-cloak>
            <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-8 border border-white/20">
                <div class="w-10 h-10 border-4 border-white/20 border-t-gold-400 rounded-full animate-spin"></div>
            </div>
            <h2 class="text-2xl font-bold mb-2">Memproses...</h2>
            <p class="text-slate-400">Menyimpan presensi</p>
            <p class="text-sm font-semibold text-gold-400 mt-2" x-text="loadingName"></p>
        </div>

        {{-- State: SUCCESS --}}
        <div x-show="state === 'success'" class="fade-in max-w-md w-full" x-cloak>
            <div class="bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20 p-8 text-center shadow-2xl">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center animate-scale-in"
                         :class="result.attendance_type === 'keluar' ? 'bg-blue-500' : 'bg-green-500'">
                        <i class="w-12 h-12 text-white" :data-lucide="result.attendance_type === 'keluar' ? 'log-out' : 'log-in'"></i>
                    </div>
                </div>

                <h2 class="text-3xl font-bold mb-1" x-text="result.attendance_type === 'keluar' ? 'Presensi Keluar' : 'Presensi Masuk'"></h2>
                <p class="text-xl font-semibold text-gold-400 mt-2 mb-6" x-text="result.teacher_name"></p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white/10 rounded-2xl p-4">
                        <p class="text-xs uppercase text-slate-400 font-bold mb-1">Jam</p>
                        <p class="text-2xl font-bold" x-text="result.time ? result.time.substring(0,5) + ' WIB' : '-'"></p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4">
                        <p class="text-xs uppercase text-slate-400 font-bold mb-1">Status</p>
                        <p class="text-lg font-bold" x-text="result.status"></p>
                    </div>
                </div>

                <p class="text-sm text-slate-400">Scan guru berikutnya...</p>
                <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full bg-gold-400 rounded-full transition-all ease-linear" :style="`width: ${countdown}%; transition-duration: ${countdownMs}ms`"></div>
                </div>
            </div>
        </div>

        {{-- State: ERROR --}}
        <div x-show="state === 'error'" class="fade-in max-w-md w-full" x-cloak>
            <div class="bg-red-500/20 backdrop-blur-sm rounded-3xl border border-red-500/40 p-8 text-center shadow-2xl">
                <div class="w-24 h-24 rounded-full bg-red-500 flex items-center justify-center mx-auto mb-6 animate-scale-in">
                    <i data-lucide="x" class="w-12 h-12 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold mb-3 text-red-200">Gagal</h2>
                <p class="text-red-300 text-base leading-relaxed" x-text="errorMsg"></p>
                <p class="text-sm text-slate-400 mt-6">Scan ulang atau hubungi admin.</p>
                <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full bg-red-400 rounded-full transition-all ease-linear" :style="`width: ${countdown}%; transition-duration: ${countdownMs}ms`"></div>
                </div>
            </div>
        </div>

        {{-- State: WARNING (sudah absen, dll.) --}}
        <div x-show="state === 'warning'" class="fade-in max-w-md w-full" x-cloak>
            <div class="bg-amber-500/20 backdrop-blur-sm rounded-3xl border border-amber-500/40 p-8 text-center shadow-2xl">
                <div class="w-24 h-24 rounded-full bg-amber-500 flex items-center justify-center mx-auto mb-6 animate-scale-in">
                    <i data-lucide="alert-triangle" class="w-12 h-12 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold mb-3 text-amber-200">Perhatian</h2>
                <p class="text-amber-300 text-base leading-relaxed" x-text="errorMsg"></p>
                <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full bg-amber-400 rounded-full transition-all ease-linear" :style="`width: ${countdown}%; transition-duration: ${countdownMs}ms`"></div>
                </div>
            </div>
        </div>

    </main>

    {{-- Footer --}}
    <footer class="px-8 py-4 border-t border-white/10 text-center text-xs text-slate-500">
        Mode Kiosk — Halaman ini tidak memerlukan login &bull; SMK ICB Cinta Teknika &copy; {{ date('Y') }}
    </footer>

</div>

<script>
function kioskApp() {
    return {
        state: 'idle',      // idle | loading | success | error | warning
        result: {},
        errorMsg: '',
        loadingName: '',
        currentTime: '',
        currentDate: '',
        countdown: 100,
        countdownMs: 3000,
        countdownTimer: null,
        bufferTimer: null,
        buffer: '',

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.bindScanner();
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },

        bindScanner() {
            const input = document.getElementById('kiosk-input');

            // Fokus ke input tersembunyi saat halaman dimuat
            const refocus = () => {
                setTimeout(() => input && input.focus(), 100);
            };
            refocus();

            // Refocus setiap kali klik di mana saja
            document.addEventListener('click', refocus);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refocus();
            });

            // Tangkap Enter dari barcode gun
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const qrData = input.value.trim();
                    input.value = '';
                    if (qrData) this.processQr(qrData);
                }
            });

            // Fallback: kalau barcode gun ngetik cepat ke dokumen bukan ke input
            let docBuffer = '';
            let docTimer = null;
            document.addEventListener('keypress', (e) => {
                if (document.activeElement === input) return; // sudah di-handle input
                if (e.key === 'Enter') {
                    if (docBuffer.trim()) {
                        const data = docBuffer.trim();
                        docBuffer = '';
                        this.processQr(data);
                    }
                    return;
                }
                docBuffer += e.key;
                clearTimeout(docTimer);
                docTimer = setTimeout(() => { docBuffer = ''; }, 500);
            });
        },

        processQr(raw) {
            if (this.state === 'loading') return; // cegah double scan

            let teacherId = null;
            let token = null;

            try {
                const json = JSON.parse(raw);
                teacherId = json.teacher_id;
                token = json.token ?? null;
            } catch {
                teacherId = raw;
            }

            if (!teacherId) {
                this.showError('Format QR tidak dikenali.');
                return;
            }

            this.state = 'loading';
            this.loadingName = '';
            if (window.lucide) lucide.createIcons();

            fetch('{{ route("kiosk.attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    qr_data: raw,
                    mode: 'auto',
                }),
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({ message: 'Respons server tidak valid.' }));
                if (!res.ok || !data.success) throw data;
                this.showSuccess(data);
            })
            .catch((err) => {
                const msg = err.message || 'Terjadi kesalahan. Silakan scan ulang.';
                // Bedakan warning (sudah absen) vs error sistem
                const isWarning = msg.includes('sudah') || msg.includes('lengkap') || msg.includes('libur') || msg.includes('dijadwalkan') || msg.includes('belum');
                if (isWarning) {
                    this.showWarning(msg);
                } else {
                    this.showError(msg);
                }
            });
        },

        showSuccess(data) {
            this.result = data;
            this.state = 'success';
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
            this.startCountdown(3000, () => this.reset());
        },

        showError(msg) {
            this.errorMsg = msg;
            this.state = 'error';
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
            this.startCountdown(4000, () => this.reset());
        },

        showWarning(msg) {
            this.errorMsg = msg;
            this.state = 'warning';
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
            this.startCountdown(4000, () => this.reset());
        },

        startCountdown(ms, cb) {
            clearTimeout(this.countdownTimer);
            this.countdown = 100;
            this.countdownMs = ms;
            this.$nextTick(() => {
                // Trigger CSS transition
                setTimeout(() => { this.countdown = 0; }, 50);
            });
            this.countdownTimer = setTimeout(cb, ms);
        },

        reset() {
            clearTimeout(this.countdownTimer);
            this.state = 'idle';
            this.result = {};
            this.errorMsg = '';
            this.loadingName = '';
            this.countdown = 100;
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
                document.getElementById('kiosk-input')?.focus();
            });
        },
    };
}
</script>
</body>
</html>
