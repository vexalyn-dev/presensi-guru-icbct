@extends(activeLayout())
@section('page-title', 'Download Aplikasi')

@section('content')
<div class="min-h-screen -m-6 bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800">

    {{-- ── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-slate-900 px-6 py-20 text-center">

        {{-- Decorative blobs --}}
        <div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 rounded-full bg-gold-400/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-navy-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-white/[0.02] border border-white/5"></div>

        {{-- Floating rings --}}
        <div class="apk-ring pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[480px] h-[480px] rounded-full border border-gold-400/10"></div>
        <div class="apk-ring-slow pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[360px] h-[360px] rounded-full border border-white/5"></div>

        <div class="relative z-10 max-w-2xl mx-auto">
            {{-- App icon --}}
            <div class="apk-icon-bounce w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gold-400 to-gold-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-gold-400/30">
                <i data-lucide="smartphone" class="w-12 h-12 text-navy-900"></i>
            </div>

            <div class="apk-fade-up">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-3 tracking-tight leading-tight">
                    ICB CT <span class="text-gold-400">Presensi</span>
                </h1>
                <p class="text-lg text-white/60 mb-2 font-medium">Aplikasi Presensi Guru — SMK ICB Cinta Teknika</p>
                <div class="flex items-center justify-center gap-3 mb-8 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white/70 text-xs font-semibold border border-white/10">
                        <i data-lucide="tag" class="w-3 h-3"></i> v1.0.0
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white/70 text-xs font-semibold border border-white/10">
                        <i data-lucide="android" class="w-3 h-3"></i> Android 8.0+
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white/70 text-xs font-semibold border border-white/10">
                        <i data-lucide="hard-drive" class="w-3 h-3"></i> ~12 MB
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── DOWNLOAD CARD ────────────────────────────────────────────────── --}}
    <div class="relative z-10 -mt-8 px-6 pb-10 max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-navy-900/20 dark:shadow-black/40 border border-slate-100 dark:border-slate-700 overflow-hidden apk-card-in">

            {{-- Top accent --}}
            <div class="h-1 w-full bg-gradient-to-r from-gold-400 via-gold-500 to-amber-400"></div>

            <div class="p-8">
                {{-- Tombol Download utama --}}
                <a href="{{ env('APK_DOWNLOAD_URL', '#') }}"
                   id="apk-btn"
                   class="apk-dl-btn group relative w-full flex items-center justify-center gap-3 px-8 py-5 bg-gradient-to-r from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-2xl text-base font-bold transition-all shadow-xl shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-2xl hover:-translate-y-0.5 active:scale-[.98] overflow-hidden">
                    <span class="apk-btn-shine pointer-events-none absolute inset-0 rounded-2xl"></span>
                    <div class="w-10 h-10 bg-white/15 dark:bg-navy-900/20 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                        <i data-lucide="download" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-[11px] font-medium opacity-70 leading-none mb-0.5">Unduh Sekarang</div>
                        <div class="text-lg font-extrabold leading-none tracking-tight">Install APK</div>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 ml-auto transition-transform group-hover:translate-x-1"></i>
                </a>

                {{-- Info grid --}}
                <div class="grid grid-cols-3 gap-3 mt-6">
                    <div class="apk-info-card flex flex-col items-center gap-1.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <p class="text-[11px] font-bold text-navy-800 dark:text-white text-center">Aman</p>
                        <p class="text-[10px] text-slate-400 text-center leading-tight">Terverifikasi developer</p>
                    </div>
                    <div class="apk-info-card flex flex-col items-center gap-1.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <i data-lucide="zap" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                        </div>
                        <p class="text-[11px] font-bold text-navy-800 dark:text-white text-center">Ringan</p>
                        <p class="text-[10px] text-slate-400 text-center leading-tight">Ukuran kecil ~12 MB</p>
                    </div>
                    <div class="apk-info-card flex flex-col items-center gap-1.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <p class="text-[11px] font-bold text-navy-800 dark:text-white text-center">Update</p>
                        <p class="text-[10px] text-slate-400 text-center leading-tight">Selalu diperbarui</p>
                    </div>
                </div>

                {{-- Panduan install --}}
                <div class="mt-6 p-5 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50 rounded-2xl">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-400">Cara Install APK</p>
                    </div>
                    <ol class="space-y-2">
                        @foreach([
                            ['icon' => 'download-cloud', 'text' => 'Klik tombol Install APK di atas untuk mengunduh file'],
                            ['icon' => 'settings',       'text' => 'Buka Pengaturan → Keamanan → aktifkan "Sumber Tidak Dikenal"'],
                            ['icon' => 'folder-open',    'text' => 'Buka file APK dari folder Downloads'],
                            ['icon' => 'check-circle',   'text' => 'Ikuti proses instalasi hingga selesai'],
                        ] as $i => $step)
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-200 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-[10px] font-black text-amber-700 dark:text-amber-400">{{ $i + 1 }}</span>
                            </div>
                            <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">{{ $step['text'] }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FITUR UNGGULAN ──────────────────────────────────────────────── --}}
    <div class="px-6 pb-10 max-w-3xl mx-auto">
        <h2 class="text-xl font-extrabold text-navy-800 dark:text-white mb-5 text-center">Fitur Unggulan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['icon'=>'scan-line',     'color'=>'bg-blue-100 dark:bg-blue-900/30',   'ic'=>'text-blue-600 dark:text-blue-400',   'title'=>'Presensi QR Code',    'desc'=>'Scan QR untuk absensi harian cepat & akurat'],
                ['icon'=>'map-pin',       'color'=>'bg-green-100 dark:bg-green-900/30', 'ic'=>'text-green-600 dark:text-green-400', 'title'=>'Validasi Lokasi GPS', 'desc'=>'Presensi hanya valid di area sekolah'],
                ['icon'=>'bell',          'color'=>'bg-purple-100 dark:bg-purple-900/30','ic'=>'text-purple-600 dark:text-purple-400','title'=>'Notifikasi Real-time','desc'=>'Dapat info jadwal & pengumuman langsung'],
                ['icon'=>'calendar-days', 'color'=>'bg-amber-100 dark:bg-amber-900/30', 'ic'=>'text-amber-600 dark:text-amber-400', 'title'=>'Riwayat Lengkap',    'desc'=>'Lihat rekap presensi bulanan & tahunan'],
                ['icon'=>'file-text',     'color'=>'bg-rose-100 dark:bg-rose-900/30',   'ic'=>'text-rose-600 dark:text-rose-400',   'title'=>'Pengajuan Izin',      'desc'=>'Ajukan izin & sakit langsung dari HP'],
                ['icon'=>'shield-check',  'color'=>'bg-teal-100 dark:bg-teal-900/30',   'ic'=>'text-teal-600 dark:text-teal-400',   'title'=>'Data Aman',           'desc'=>'Terenkripsi & tersimpan di server sekolah'],
            ] as $feat)
            <div class="apk-feature-card flex items-start gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 {{ $feat['color'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $feat['icon'] }}" class="w-5 h-5 {{ $feat['ic'] }}"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-navy-800 dark:text-white">{{ $feat['title'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $feat['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── REQUIREMENTS ────────────────────────────────────────────────── --}}
    <div class="px-6 pb-10 max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm">
            <h3 class="text-base font-extrabold text-navy-800 dark:text-white mb-4 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-gold-500"></i>
                Spesifikasi Minimum
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['label'=>'OS',        'val'=>'Android 8.0+'],
                    ['label'=>'RAM',       'val'=>'2 GB'],
                    ['label'=>'Storage',   'val'=>'50 MB free'],
                    ['label'=>'Kamera',    'val'=>'Diperlukan'],
                    ['label'=>'Internet',  'val'=>'Diperlukan'],
                    ['label'=>'GPS',       'val'=>'Diperlukan'],
                ] as $req)
                <div class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-gold-500 flex-shrink-0"></i>
                    <div>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wide font-semibold">{{ $req['label'] }}</p>
                        <p class="text-xs font-bold text-navy-800 dark:text-white">{{ $req['val'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── FOOTER NOTE ─────────────────────────────────────────────────── --}}
    <div class="px-6 pb-16 max-w-3xl mx-auto text-center">
        <p class="text-xs text-slate-400 dark:text-slate-500">
            Dikembangkan oleh <span class="font-semibold text-navy-700 dark:text-slate-300">Vexalyn Dev</span>
            &nbsp;·&nbsp; SMK ICB Cinta Teknika &nbsp;·&nbsp; v1.0.0
        </p>
    </div>

</div>

<style>
/* ── Hero animations ── */
.apk-icon-bounce {
    animation: apkIconBounce 3s ease-in-out infinite;
}
@keyframes apkIconBounce {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
}
.apk-ring {
    animation: apkRingSpin 18s linear infinite;
}
.apk-ring-slow {
    animation: apkRingSpin 28s linear infinite reverse;
}
@keyframes apkRingSpin {
    from { transform: translate(-50%,-50%) rotate(0deg); }
    to   { transform: translate(-50%,-50%) rotate(360deg); }
}
.apk-fade-up {
    animation: apkFadeUp 0.7s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes apkFadeUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.apk-card-in {
    animation: apkCardIn 0.6s cubic-bezier(0.22,1,0.36,1) 0.15s both;
}
@keyframes apkCardIn {
    from { opacity:0; transform:translateY(32px) scale(0.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
.apk-feature-card {
    animation: apkFadeUp 0.5s ease both;
}
.apk-info-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.apk-info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(15,23,42,0.08);
}

/* ── Download button shine effect ── */
.apk-btn-shine::before {
    content: '';
    position: absolute;
    top: 0; left: -80%;
    width: 60%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.12) 50%, transparent 60%);
    transition: left 0.6s ease;
}
.apk-dl-btn:hover .apk-btn-shine::before {
    left: 130%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) lucide.createIcons();

    // Stagger animation feature cards
    document.querySelectorAll('.apk-feature-card').forEach(function(el, i) {
        el.style.animationDelay = (i * 0.08) + 's';
    });

    // Download button — progress feedback
    var btn = document.getElementById('apk-btn');
    if (btn) {
        btn.addEventListener('click', function(e) {
            var href = btn.getAttribute('href');
            if (!href || href === '#') {
                e.preventDefault();
                // APK belum tersedia — tampilkan toast
                var toast = document.createElement('div');
                toast.className = 'fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 bg-amber-500 text-white rounded-2xl shadow-2xl text-sm font-semibold';
                toast.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> APK belum tersedia. Segera hadir!';
                document.body.appendChild(toast);
                setTimeout(function() {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s';
                    setTimeout(function() { toast.remove(); }, 300);
                }, 3000);
            }
        });
    }
});
</script>
@endsection
