<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { navy: { 800: '#1e3a5f', 900: '#0f2340' }, gold: { 400: '#facc15' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.12; animation: blobFloat 8s ease-in-out infinite; }
        .blob-1 { width: 400px; height: 400px; background: #dc2626; top: -100px; left: -100px; }
        .blob-2 { width: 300px; height: 300px; background: #1e3a5f; bottom: -80px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 200px; height: 200px; background: #dc2626; bottom: 100px; left: 100px; animation-delay: 5s; }
        @keyframes blobFloat { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(20px,-20px) scale(1.05)} 66%{transform:translate(-15px,10px) scale(0.95)} }
        .num-anim { animation: numPop 0.7s cubic-bezier(0.34,1.56,0.64,1) both; }
        @keyframes numPop { 0%{opacity:0;transform:scale(0.5) translateY(30px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
        .illus-wrap { animation: illustFloat 4s ease-in-out infinite; }
        @keyframes illustFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
        .star { position: absolute; background: #1e3a5f; border-radius: 50%; animation: twinkle 2s ease-in-out infinite; }
        @keyframes twinkle { 0%,100%{opacity:.15;transform:scale(1)} 50%{opacity:.7;transform:scale(1.4)} }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    @for ($i = 0; $i < 12; $i++)
    <div class="star" style="width:{{ rand(3,7) }}px;height:{{ rand(3,7) }}px;top:{{ rand(5,90) }}%;left:{{ rand(5,90) }}%;animation-delay:{{ $i*0.3 }}s;"></div>
    @endfor

    <div class="relative z-10 text-center px-6 max-w-lg w-full">

        <div class="illus-wrap mb-6 fade-up" style="animation-delay:.1s">
            <img src="{{ asset('mascot/akses di tolak 403.png') }}"
                 alt="Akses Ditolak"
                 class="w-64 h-auto mx-auto object-contain drop-shadow-lg select-none">
        </div>

        <div class="num-anim mb-2" style="animation-delay:.15s">
            <span class="text-8xl font-black text-red-600 leading-none tracking-tighter select-none">403</span>
        </div>

        <h1 class="text-2xl font-bold text-navy-800 mb-3 fade-up" style="animation-delay:.25s">
            Akses Ditolak! 🚫
        </h1>

        <p class="text-slate-500 text-base leading-relaxed mb-8 fade-up" style="animation-delay:.35s">
            Kamu tidak punya izin untuk mengakses halaman ini.<br>
            Hubungi administrator jika kamu merasa ini kesalahan.
        </p>

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

        <p class="text-xs text-slate-300 mt-8 fade-up" style="animation-delay:.55s">ICB CT — Sistem Presensi Guru</p>
    </div>
</body>
</html>
