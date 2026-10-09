<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — ICB CT</title>
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        *, body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Subtle navy blob only — no yellow */
        .blob-navy {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.06;
            background: #1e3a5f;
            animation: blobPulse 10s ease-in-out infinite;
        }
        .blob-1 { width: 500px; height: 500px; top: -180px; left: -180px; animation-delay: 0s; }
        .blob-2 { width: 350px; height: 350px; bottom: -120px; right: -100px; animation-delay: 5s; }
        @keyframes blobPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }

        /* Gear spin */
        .gear-spin-slow { animation: spin 10s linear infinite; transform-box: fill-box; transform-origin: center; }
        .gear-spin-fast { animation: spin 5s linear infinite reverse; transform-box: fill-box; transform-origin: center; }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

        /* Illustration float */
        .illus-float { animation: float 3.5s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }

        /* Fade up stagger */
        .fade-up { opacity: 0; animation: fadeUp 0.65s ease-out forwards; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }

        /* Progress bar — gold */
        .progress-anim {
            height: 100%;
            background: #facc15;
            border-radius: 9999px;
            animation: progAnim 2.8s ease-in-out infinite;
        }
        @keyframes progAnim { 0%{width:8%} 50%{width:82%} 85%{width:92%} 100%{width:8%} }

        /* Pulse dot */
        .pulse-dot { animation: pulseDot 2s ease-in-out infinite; }
        @keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }
    </style>
</head>
<body>
    <!-- Subtle blobs -->
    <div class="blob-navy blob-1"></div>
    <div class="blob-navy blob-2"></div>

    <!-- Main content — full width, centered, padded -->
    <div class="relative z-10 w-full max-w-md mx-auto px-6 py-12 text-center">

        <!-- Badge — smaller, not too high -->
        <div class="flex justify-center mb-6 fade-up" style="animation-delay:.05s">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-amber-50 border border-amber-200 rounded-full">
                <span class="pulse-dot w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                <span class="text-amber-700 text-xs font-bold uppercase tracking-wider">Sistem Dalam Pemeliharaan</span>
            </div>
        </div>

        <!-- Illustration -->
        <div class="illus-float mb-6 fade-up" style="animation-delay:.1s">
            <img src="/mascot/maintenance.png"
                 alt="Maintenance"
                 class="w-64 sm:w-72 h-auto mx-auto object-contain drop-shadow-lg select-none">
        </div>

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-tight fade-up" style="animation-delay:.2s">
            Sistem Sedang<br>Dalam Pemeliharaan
        </h1>

        <!-- Message -->
        <p class="text-gray-500 text-sm sm:text-base leading-relaxed mb-8 fade-up" style="animation-delay:.3s">
            {{ $message ?? 'Kami sedang melakukan pemeliharaan untuk meningkatkan kualitas layanan. Mohon bersabar dan coba beberapa saat lagi.' }}
        </p>

        <!-- Progress bar — gold solid -->
        <div class="mb-8 fade-up" style="animation-delay:.38s">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Pemeliharaan</span>
                <span class="text-xs font-bold text-amber-500">Sedang Berjalan...</span>
            </div>
            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="progress-anim"></div>
            </div>
        </div>

        <!-- Info cards -->
        <div class="grid grid-cols-3 gap-3 mb-8 fade-up" style="animation-delay:.46s">
            <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm">
                <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Data Aman</p>
                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Tidak ada data yang hilang</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm">
                <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Segera Selesai</p>
                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Estimasi waktu singkat</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm">
                <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Lebih Cepat</p>
                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Setelah maintenance</p>
            </div>
        </div>

        <!-- Countdown -->
        <div class="fade-up" style="animation-delay:.54s">
            <p class="text-xs text-gray-400 mb-2">Halaman diperbarui otomatis dalam</p>
            <div class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl shadow-sm">
                <span class="text-2xl font-black text-gray-800 tabular-nums" id="countdown">60</span>
                <span class="text-sm text-gray-400 font-medium">detik</span>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-[11px] text-gray-300 mt-8 fade-up" style="animation-delay:.6s">
            &copy; {{ date('Y') }} SMK ICB Cinta Teknika &mdash; Developed By Vexalyn Dev
        </p>
    </div>

    <script>
        var sec = 60;
        var el  = document.getElementById('countdown');
        setInterval(function() {
            sec--;
            if (el) el.textContent = sec;
            if (sec <= 0) window.location.reload();
        }, 1000);
    </script>
</body>
</html>
