<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1e3a5f', 900: '#0f2340' },
                        gold: { 400: '#facc15', 500: '#eab308' }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }

        body { background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }

        /* Floating blobs */
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.12; animation: blobFloat 8s ease-in-out infinite; }
        .blob-1 { width: 400px; height: 400px; background: #1e3a5f; top: -100px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 300px; height: 300px; background: #facc15; bottom: -80px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 200px; height: 200px; background: #1e3a5f; bottom: 100px; left: 100px; animation-delay: 5s; }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -20px) scale(1.05); }
            66% { transform: translate(-15px, 10px) scale(0.95); }
        }

        /* 404 number animation */
        .num-404 { animation: numPop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both; }
        @keyframes numPop {
            0% { opacity: 0; transform: scale(0.5) translateY(30px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Illustration float */
        .illus-wrap { animation: illustFloat 4s ease-in-out infinite; }
        @keyframes illustFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }

        /* Content fade-up */
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stars twinkle */
        .star { position: absolute; background: #1e3a5f; border-radius: 50%; animation: twinkle 2s ease-in-out infinite; }
        @keyframes twinkle { 0%,100%{opacity:.15;transform:scale(1)} 50%{opacity:.7;transform:scale(1.4)} }
    </style>
</head>
<body>
    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Stars scattered -->
    @for ($i = 0; $i < 12; $i++)
    <div class="star" style="
        width: {{ rand(3,7) }}px; height: {{ rand(3,7) }}px;
        top: {{ rand(5,90) }}%; left: {{ rand(5,90) }}%;
        animation-delay: {{ $i * 0.3 }}s;
    "></div>
    @endfor

    <div class="relative z-10 text-center px-6 max-w-lg w-full">

        <!-- Illustration -->
        <div class="illus-wrap mb-6 fade-up" style="animation-delay:.1s">
            <svg viewBox="0 0 320 240" class="w-72 h-auto mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Ground shadow -->
                <ellipse cx="160" cy="225" rx="90" ry="10" fill="#1e3a5f" opacity="0.08"/>

                <!-- Road / path -->
                <rect x="100" y="190" width="120" height="30" rx="8" fill="#e2e8f0"/>
                <rect x="150" y="195" width="20" height="5" rx="2" fill="#cbd5e1"/>
                <rect x="150" y="208" width="20" height="5" rx="2" fill="#cbd5e1"/>

                <!-- Character body -->
                <ellipse cx="160" cy="180" rx="22" ry="28" fill="#1e3a5f"/>
                <!-- Head -->
                <circle cx="160" cy="145" r="22" fill="#fde68a"/>
                <!-- Eyes (X X = confused) -->
                <text x="151" y="148" font-size="10" fill="#1e3a5f" font-weight="bold">×</text>
                <text x="163" y="148" font-size="10" fill="#1e3a5f" font-weight="bold">×</text>
                <!-- Mouth -->
                <path d="M153 156 Q160 152 167 156" stroke="#1e3a5f" stroke-width="2" stroke-linecap="round" fill="none"/>
                <!-- Sweat drop -->
                <path d="M180 138 Q183 130 186 138 Q186 143 183 143 Q180 143 180 138Z" fill="#93c5fd"/>

                <!-- Arms -->
                <path d="M138 170 Q125 160 120 155" stroke="#1e3a5f" stroke-width="8" stroke-linecap="round"/>
                <path d="M182 170 Q195 160 200 155" stroke="#1e3a5f" stroke-width="8" stroke-linecap="round"/>
                <!-- Hand L (pointing up confused) -->
                <circle cx="120" cy="153" r="7" fill="#fde68a"/>
                <!-- Hand R -->
                <circle cx="200" cy="153" r="7" fill="#fde68a"/>

                <!-- Legs -->
                <rect x="148" y="205" width="10" height="20" rx="5" fill="#1e3a5f"/>
                <rect x="162" y="205" width="10" height="20" rx="5" fill="#1e3a5f"/>

                <!-- Question marks floating -->
                <text x="60" y="120" font-size="28" fill="#facc15" opacity="0.7" style="animation: twinkle 2s ease-in-out infinite;">?</text>
                <text x="235" y="110" font-size="20" fill="#1e3a5f" opacity="0.4" style="animation: twinkle 2.5s ease-in-out infinite 1s;">?</text>
                <text x="80" y="80" font-size="14" fill="#1e3a5f" opacity="0.3" style="animation: twinkle 3s ease-in-out infinite .5s;">?</text>

                <!-- Signpost -->
                <rect x="230" y="130" width="6" height="60" rx="3" fill="#94a3b8"/>
                <rect x="218" y="130" width="50" height="22" rx="6" fill="#facc15"/>
                <text x="243" y="145" font-size="8" fill="#1e3a5f" font-weight="bold" text-anchor="middle">LOST!</text>
            </svg>
        </div>

        <!-- 404 number -->
        <div class="num-404 mb-2" style="animation-delay:.15s">
            <span class="text-8xl font-black text-navy-800 leading-none tracking-tighter select-none">404</span>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-navy-800 mb-3 fade-up" style="animation-delay:.25s">
            Waduh, nyasar nih! 😅
        </h1>

        <!-- Subtitle -->
        <p class="text-slate-500 text-base leading-relaxed mb-8 fade-up" style="animation-delay:.35s">
            Halaman yang kamu cari kayaknya udah pindah, dihapus, atau emang gak pernah ada.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-up" style="animation-delay:.45s">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Balik Lagi
            </a>
            <a href="{{ auth()->check() ? (auth()->user()->canAccessAdmin() ? route('dashboard') : route('teacher.dashboard')) : route('login') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 hover:opacity-90 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Ke Beranda
            </a>
        </div>

        <!-- Footer hint -->
        <p class="text-xs text-slate-300 mt-8 fade-up" style="animation-delay:.55s">
            ICB CT — Sistem Presensi Guru
        </p>
    </div>
</body>
</html>
