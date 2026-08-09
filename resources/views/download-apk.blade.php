@extends(activeLayout())
@section('page-title', 'Download Aplikasi')
@section('content')
@php
    $apkSetting = \App\Models\AppSetting::getInstance();
    // Fallback ke Setting key-value jika kolom APK belum ada (migration belum jalan)
    $hasApkCol = \Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'apk_file');
    if (!$hasApkCol || (!$apkSetting->apk_file && \App\Models\Setting::get('apk_file_path'))) {
        $apkSetting->apk_file        = \App\Models\Setting::get('apk_file_path');
        $apkSetting->apk_name        = \App\Models\Setting::get('apk_name');
        $apkSetting->apk_version     = \App\Models\Setting::get('apk_version');
        $apkSetting->apk_min_android = \App\Models\Setting::get('apk_min_android');
        $apkSetting->apk_size        = (int) \App\Models\Setting::get('apk_size', 0);
        $apkSetting->apk_changelog   = \App\Models\Setting::get('apk_changelog');
    }
    $apkDownloadUrl = $apkSetting->apk_file
        ? asset('storage/' . $apkSetting->apk_file)
        : (\App\Models\Setting::get('apk_download_url') ?: '');
@endphp

{{-- ════════════════════════════════════════════════════════ --}}
{{--  BANNER SLIDER                                          --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="relative overflow-hidden rounded-2xl sm:rounded-3xl mb-6 shadow-xl shadow-navy-900/20"
     style="height:210px;min-height:210px;"
     id="apk-slider">

    {{-- Slide 1: Presensi Mudah --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-5 sm:px-12"
         style="background:linear-gradient(135deg,#0A1628 0%,#0F2847 60%,#1E3A5C 100%);">
        <div class="z-10 max-w-[200px] sm:max-w-sm">
            <div class="inline-flex items-center gap-1.5 mb-3 sm:mb-4">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(250,204,21,0.2);border:1px solid rgba(250,204,21,0.35);">
                    <i data-lucide="sparkles" class="w-2.5 h-2.5 sm:w-3 sm:h-3" style="color:#FDE68A;"></i>
                </div>
                <span class="text-[10px] sm:text-xs font-bold tracking-wide uppercase" style="color:#FDE68A;letter-spacing:0.06em;">Aplikasi Resmi</span>
            </div>
            <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-1.5 sm:mb-2">Presensi Guru<br><span style="color:#FACC15;">Lebih Mudah</span></h2>
            <p class="text-[10px] sm:text-sm leading-relaxed" style="color:rgba(255,255,255,0.55);">Scan QR, validasi GPS, semua bisa dari HP kamu kapan aja.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-36 h-36 md:w-44 md:h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" class="w-full h-full opacity-80">
                <rect x="50" y="20" width="80" height="140" rx="16" fill="#FACC15" fill-opacity=".1" stroke="#FACC15" stroke-width="2.5"/>
                <rect x="62" y="36" width="56" height="88" rx="6" fill="#0F172A"/>
                <circle cx="90" cy="142" r="6" fill="#FACC15" fill-opacity=".6"/>
                <rect x="74" y="28" width="32" height="4" rx="2" fill="#FACC15" fill-opacity=".4"/>
                <circle cx="90" cy="95" r="16" fill="#FACC15" fill-opacity=".15" stroke="#FACC15" stroke-width="1.8"/>
                <path d="M84 95l4 4 8-8" stroke="#FACC15" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -bottom-20 -right-20 w-64 h-64 rounded-full blur-3xl" style="background:rgba(250,204,21,0.05);"></div>
    </div>

    {{-- Slide 2: GPS Validasi --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-5 sm:px-12"
         style="background:linear-gradient(135deg,#0A1628 0%,#0F2847 60%,#1E3A5C 100%);">
        <div class="z-10 max-w-[200px] sm:max-w-sm">
            <div class="inline-flex items-center gap-1.5 mb-3 sm:mb-4">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(250,204,21,0.2);border:1px solid rgba(250,204,21,0.35);">
                    <i data-lucide="map-pin" class="w-2.5 h-2.5 sm:w-3 sm:h-3" style="color:#FDE68A;"></i>
                </div>
                <span class="text-[10px] sm:text-xs font-bold tracking-wide uppercase" style="color:#FDE68A;letter-spacing:0.06em;">GPS Validasi</span>
            </div>
            <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-1.5 sm:mb-2">Absensi Hanya<br><span style="color:#FACC15;">Di Area Sekolah</span></h2>
            <p class="text-[10px] sm:text-sm leading-relaxed" style="color:rgba(255,255,255,0.55);">Sistem otomatis cek lokasi sebelum absensi diproses.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-36 h-36 md:w-44 md:h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" class="w-full h-full opacity-80">
                <circle cx="90" cy="75" r="42" fill="#FACC15" fill-opacity=".08" stroke="#FACC15" stroke-width="2"/>
                <circle cx="90" cy="75" r="26" fill="#FACC15" fill-opacity=".12" stroke="#FACC15" stroke-width="2"/>
                <circle cx="90" cy="75" r="11" fill="#FACC15" fill-opacity=".35"/>
                <path d="M90 75v50" stroke="#FACC15" stroke-width="2.2" stroke-linecap="round" stroke-dasharray="4 4"/>
                <ellipse cx="90" cy="128" rx="20" ry="7" fill="#FACC15" fill-opacity=".18"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -top-20 -left-20 w-64 h-64 rounded-full blur-3xl" style="background:rgba(250,204,21,0.04);"></div>
    </div>

    {{-- Slide 3: Notifikasi Real-time --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-5 sm:px-12"
         style="background:linear-gradient(135deg,#0A1628 0%,#0F2847 60%,#1E3A5C 100%);">
        <div class="z-10 max-w-[200px] sm:max-w-sm">
            <div class="inline-flex items-center gap-1.5 mb-3 sm:mb-4">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(250,204,21,0.2);border:1px solid rgba(250,204,21,0.35);">
                    <i data-lucide="bell" class="w-2.5 h-2.5 sm:w-3 sm:h-3" style="color:#FDE68A;"></i>
                </div>
                <span class="text-[10px] sm:text-xs font-bold tracking-wide uppercase" style="color:#FDE68A;letter-spacing:0.06em;">Real-time</span>
            </div>
            <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-1.5 sm:mb-2">Notifikasi<br><span style="color:#FACC15;">Langsung Masuk</span></h2>
            <p class="text-[10px] sm:text-sm leading-relaxed" style="color:rgba(255,255,255,0.55);">Jadwal, izin & pengumuman langsung muncul di HP kamu.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-36 h-36 md:w-44 md:h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" class="w-full h-full opacity-80">
                <path d="M90 30 C65 30 48 50 48 72 L48 110 L36 122 L144 122 L132 110 L132 72 C132 50 115 30 90 30Z" fill="#FACC15" fill-opacity=".12" stroke="#FACC15" stroke-width="2.2"/>
                <path d="M82 122 Q82 132 90 132 Q98 132 98 122" stroke="#FACC15" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                <circle cx="126" cy="46" r="13" fill="#FACC15" fill-opacity=".7"/>
                <path d="M121 46h5m-2.5-2.5v5" stroke="#0F172A" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-3xl" style="background:rgba(250,204,21,0.04);"></div>
    </div>

    {{-- Slide 4: Developer — dark purple premium --}}
    <div class="apk-slide absolute inset-0 overflow-hidden"
         style="background:linear-gradient(135deg,#0D0618 0%,#160D2E 45%,#2A1455 100%);">
        <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 rounded-full blur-3xl" style="background:rgba(139,92,246,0.12);"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-16 w-56 h-56 rounded-full blur-3xl" style="background:rgba(168,85,247,0.08);"></div>
        <div class="relative z-10 h-full flex items-center justify-between px-5 sm:px-12">
            <div class="max-w-[210px] sm:max-w-sm">
                <div class="inline-flex items-center gap-1.5 mb-3 sm:mb-4">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(139,92,246,0.25);border:1px solid rgba(139,92,246,0.45);">
                        <i data-lucide="code-2" class="w-2.5 h-2.5 sm:w-3 sm:h-3" style="color:#C4B5FD;"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold tracking-wide uppercase" style="color:#C4B5FD;letter-spacing:0.06em;">Developer</span>
                </div>
                <div class="flex items-center gap-2.5 mb-2 sm:mb-3">
                    <img src="{{ asset('images/logo-dev-banner.png') }}" alt="Vexalyn Dev"
                         class="object-contain"
                         style="height:40px;width:auto;max-width:200px;filter:invert(1) brightness(2);transform:translateX(-10px);">
                </div>
                <p class="text-[10px] sm:text-xs leading-relaxed mb-3 sm:mb-4" style="color:rgba(255,255,255,0.5);">
                    Vio Atmajaya Saputra
                </p>
                <a href="https://vexalyndev.my.id" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold transition-all hover:-translate-y-0.5 active:scale-95"
                   style="background:rgba(139,92,246,0.25);border:1px solid rgba(139,92,246,0.45);color:#DDD6FE;">
                    <i data-lucide="external-link" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                    Cek Profile Developer
                </a>
            </div>
            <div class="hidden sm:flex items-center justify-center w-36 h-36 md:w-44 md:h-44 flex-shrink-0">
                <img src="{{ asset('images/logo-dev-kotak.png') }}" alt="Vexalyn Dev"
                     class="w-full h-full object-contain"
                     style="filter:invert(1) brightness(1.8);opacity:0.75;">
            </div>
        </div>
    </div>

    {{-- Dots — modern pill style, tengah bawah --}}
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20"
         style="background:rgba(255,255,255,0.1);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15);border-radius:99px;padding:5px 8px;display:flex;align-items:center;gap:5px;">
        <button class="apk-dot transition-all duration-400" data-idx="0"
                style="width:20px;height:4px;border-radius:99px;background:rgba(255,255,255,0.95);box-shadow:0 0 6px rgba(255,255,255,0.5);"></button>
        <button class="apk-dot transition-all duration-400" data-idx="1"
                style="width:4px;height:4px;border-radius:99px;background:rgba(255,255,255,0.3);"></button>
        <button class="apk-dot transition-all duration-400" data-idx="2"
                style="width:4px;height:4px;border-radius:99px;background:rgba(255,255,255,0.3);"></button>
        <button class="apk-dot transition-all duration-400" data-idx="3"
                style="width:4px;height:4px;border-radius:99px;background:rgba(255,255,255,0.3);"></button>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{--  MAIN CONTENT — 2 col PC, 1 col mobile                 --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="apk-main mt-6">

    {{-- ── LEFT: App info ── --}}
    <div class="apk-left">

        {{-- App identity --}}
        <div class="flex items-center gap-4 mb-6">
            @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
            @if($appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-16 h-16 object-contain flex-shrink-0 rounded-2xl shadow-lg shadow-navy-800/20 dark:shadow-black/30">
            @else
                <div class="w-16 h-16 bg-gradient-to-br from-gold-400 to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-gold-400/25 flex-shrink-0">
                    <i data-lucide="school" class="w-8 h-8 text-navy-900"></i>
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-extrabold text-navy-800 dark:text-white leading-tight">{{ $apkSetting->apk_name ?? 'ICB CT' }} <span class="text-gold-500">Presensi</span></h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Presensi Guru — SMK ICB Cinta Teknika</p>
                <div class="flex gap-2 mt-1.5 flex-wrap">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $apkSetting->apk_version_label }}</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $apkSetting->apk_min_android ?? 'Android 8.0+' }}</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $apkSetting->apk_size_human }}</span>
                </div>
            </div>
        </div>

        {{-- Features grid --}}
        <h3 class="text-sm font-bold text-navy-800 dark:text-white mb-3 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-gold-500"></i> Fitur Unggulan
        </h3>
        <div class="grid grid-cols-2 gap-3 mb-6">
            @foreach([
                ['icon'=>'scan-line',    'color'=>'bg-blue-100 dark:bg-blue-900/30',   'ic'=>'text-blue-600',  'title'=>'Presensi QR Code',   'desc'=>'Scan QR cepat & akurat'],
                ['icon'=>'map-pin',      'color'=>'bg-green-100 dark:bg-green-900/30', 'ic'=>'text-green-600', 'title'=>'Validasi GPS',        'desc'=>'Hanya valid di sekolah'],
                ['icon'=>'bell',         'color'=>'bg-purple-100 dark:bg-purple-900/30','ic'=>'text-purple-600','title'=>'Notif Real-time',    'desc'=>'Info jadwal langsung'],
                ['icon'=>'calendar-days','color'=>'bg-amber-100 dark:bg-amber-900/30', 'ic'=>'text-amber-600', 'title'=>'Riwayat Lengkap',    'desc'=>'Rekap presensi bulanan'],
                ['icon'=>'file-text',    'color'=>'bg-rose-100 dark:bg-rose-900/30',   'ic'=>'text-rose-600',  'title'=>'Pengajuan Izin',      'desc'=>'Langsung dari HP'],
                ['icon'=>'shield-check', 'color'=>'bg-teal-100 dark:bg-teal-900/30',   'ic'=>'text-teal-600',  'title'=>'Data Aman',           'desc'=>'Enkripsi end-to-end'],
            ] as $f)
            <div class="flex items-start gap-3 p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="w-8 h-8 {{ $f['color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $f['icon'] }}" class="w-4 h-4 {{ $f['ic'] }}"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-navy-800 dark:text-white truncate">{{ $f['title'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Spesifikasi --}}
        <h3 class="text-sm font-bold text-navy-800 dark:text-white mb-3 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-gold-500"></i> Spesifikasi
        </h3>
        <div class="grid grid-cols-3 gap-2">
            @foreach([['OS','Android 8+'],['RAM','2 GB'],['Storage','50 MB'],['Kamera','✓'],['Internet','✓'],['GPS','✓']] as $r)
            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 text-center">
                <p class="text-[9px] text-slate-400 uppercase font-bold tracking-wide">{{ $r[0] }}</p>
                <p class="text-xs font-bold text-navy-800 dark:text-white mt-0.5">{{ $r[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── RIGHT: Download card ── --}}
    <div class="apk-right">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-navy-900/15 dark:shadow-black/40 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-gold-400 via-gold-500 to-amber-400"></div>
            <div class="p-6">

                {{-- Download button --}}
                <button id="apk-btn"
                        class="apk-dl-btn group relative w-full flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-2xl font-bold transition-all shadow-xl shadow-navy-800/30 hover:shadow-2xl hover:-translate-y-0.5 active:scale-[.98] overflow-hidden mb-5">
                    <span class="apk-shine pointer-events-none absolute inset-0 rounded-2xl"></span>
                    <div class="w-10 h-10 bg-white/15 dark:bg-navy-900/20 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i data-lucide="download" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left flex-1">
                        <div class="text-[11px] opacity-70 leading-none mb-0.5">Unduh Sekarang</div>
                        <div class="text-base font-extrabold leading-none">Install APK</div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </button>

                {{-- 3 info badges --}}
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <p class="text-[10px] font-bold text-navy-800 dark:text-white">Aman</p>
                        <p class="text-[9px] text-slate-400 text-center leading-tight">Terverifikasi developer</p>
                    </div>
                    <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i data-lucide="zap" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <p class="text-[10px] font-bold text-navy-800 dark:text-white">Ringan</p>
                        <p class="text-[9px] text-slate-400 text-center leading-tight">Ukuran kecil {{ $apkSetting->apk_size_human }}</p>
                    </div>
                    <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <p class="text-[10px] font-bold text-navy-800 dark:text-white">Update</p>
                        <p class="text-[9px] text-slate-400 text-center leading-tight">Selalu diperbarui</p>
                    </div>
                </div>

                {{-- Panduan install --}}
                <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50 rounded-2xl">
                    <p class="text-xs font-bold text-amber-800 dark:text-amber-400 mb-3 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i> Cara Install APK
                    </p>
                    <ol class="space-y-2">
                        @foreach(['Klik tombol Install APK untuk mengunduh','Buka Pengaturan → Keamanan → aktifkan "Sumber Tidak Dikenal"','Buka file APK dari folder Downloads','Ikuti proses instalasi hingga selesai'] as $i => $s)
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-200 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0 mt-0.5 text-[9px] font-black text-amber-700 dark:text-amber-400">{{ $i+1 }}</span>
                            <p class="text-[11px] text-amber-700 dark:text-amber-300 leading-relaxed">{{ $s }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-center gap-1.5 mt-4">
                    <span class="text-[10px] text-slate-400">Dikembangkan oleh</span>
                    <img src="{{ asset('images/logo-dev-banner.png') }}" alt="Vexalyn Dev"
                         class="dark:invert"
                         style="height:13px;width:auto;max-width:none;transform:translateX(-4px);">
                </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /apk-main --}}

{{-- ════════════════════════════════════════════════════════ --}}
{{--  MODALS                                                 --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="apk-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,0.75);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:16px;">
    <div id="apk-modal-box" style="background:#fff;width:100%;max-width:360px;border-radius:24px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,0.35);transform:translateY(30px) scale(0.96);opacity:0;transition:transform .3s cubic-bezier(0.22,1,0.36,1),opacity .25s ease;" class="dark:!bg-slate-900">
        <div class="h-1.5 bg-gradient-to-r from-navy-800 via-gold-400 to-amber-400"></div>

        {{-- Panel: Belum tersedia / Segera hadir --}}
        <div id="apk-panel-soon" class="p-8 text-center">
            @if($apkSetting?->apk_file)
            {{-- APK sudah tersedia tapi URL tidak diset -- fallback info --}}
            <div class="w-16 h-16 bg-navy-100 dark:bg-navy-800/50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="smartphone" class="w-8 h-8 text-navy-600 dark:text-gold-400"></i>
            </div>
            <h3 class="text-lg font-extrabold text-navy-800 dark:text-white mb-2">APK Tersedia!</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-1">
                <span class="font-semibold text-navy-800 dark:text-white">{{ $apkSetting->apk_name ?? 'ICB CT Presensi' }}</span>
                · {{ $apkSetting->apk_version_label }}
            </p>
            <p class="text-xs text-slate-400 mb-6">{{ $apkSetting->apk_size_human }} · {{ $apkSetting->apk_min_android ?? 'Android 8.0+' }}</p>
            @else
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="construction" class="w-8 h-8 text-amber-500"></i>
            </div>
            <h3 class="text-lg font-extrabold text-navy-800 dark:text-white mb-2">Bentar dulu ya! 🙏</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-2">APK-nya lagi kita siapkan biar mantap banget sebelum rilis.</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">Nantikan update-nya, ya! <span class="font-semibold text-navy-800 dark:text-slate-200">Gak bakal lama kok 🚀</span></p>
            @endif
            <button onclick="closeApkModal()" class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 active:scale-95 transition-all shadow-lg">
                {{ $apkSetting?->apk_file ? 'Tutup' : 'Siap, ditunggu!' }}
            </button>
        </div>

        {{-- Panel: Loading --}}
        <div id="apk-panel-loading" class="p-8 text-center" style="display:none">
            <div class="relative w-16 h-16 mx-auto mb-5">
                <div style="position:absolute;inset:0;border-radius:50%;border:4px solid #E2E8F0;border-top-color:#0F172A;animation:apkSpin .9s linear infinite;"></div>
                <div style="position:absolute;top:8px;left:8px;right:8px;bottom:8px;border-radius:50%;border:4px solid transparent;border-bottom-color:#FACC15;animation:apkSpinRev .7s linear infinite;"></div>
            </div>
            <div class="flex justify-center gap-1.5 mb-4">
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;display:inline-block;animation:apkDot .6s ease-in-out infinite;"></span>
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;display:inline-block;animation:apkDot .6s ease-in-out .15s infinite;"></span>
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;display:inline-block;animation:apkDot .6s ease-in-out .3s infinite;"></span>
            </div>
            <p class="text-base font-bold text-navy-800 dark:text-white mb-1">Mempersiapkan unduhan...</p>
            <p class="text-xs text-slate-400">Sebentar lagi file APK-nya mulai diunduh</p>
        </div>

        {{-- Panel: Sukses --}}
        <div id="apk-panel-done" class="p-8 text-center" style="display:none">
            <div class="w-16 h-16 mx-auto mb-5">
                <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="36" cy="36" r="32" stroke="#22C55E" stroke-width="4" fill="none" stroke-dasharray="180" stroke-dashoffset="180" style="animation:apkCircleIn .5s cubic-bezier(0.65,0,0.35,1) forwards"/>
                    <path d="M20 37l12 12 20-22" stroke="#22C55E" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none" stroke-dasharray="60" stroke-dashoffset="60" style="animation:apkCheckIn .4s ease .45s forwards"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold text-navy-800 dark:text-white mb-2">Unduhan Dimulai! 🎉</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-2">File APK udah mulai diunduh ke perangkat kamu.</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">Cek folder <span class="font-semibold text-navy-800 dark:text-slate-200">Downloads</span> terus install deh! 🙌</p>
            <button onclick="closeApkModal()" class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 active:scale-95 transition-all shadow-lg">Oke, siap install!</button>
        </div>
    </div>
</div>

<style>
/* ── Layout ── */
.apk-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
@media (min-width: 1024px) {
    .apk-main {
        display: grid;
        grid-template-columns: 1fr 400px;
        align-items: start;
        gap: 32px;
    }
    /* Banner lebih tinggi di PC */
    #apk-slider {
        height: 280px !important;
        min-height: 280px !important;
    }
}

/* ── Slider Netflix-style transition ── */
.apk-slide {
    opacity: 0;
    transform: scale(1.04) translateX(30px);
    transition: opacity 0.7s cubic-bezier(0.16,1,0.3,1),
                transform 0.7s cubic-bezier(0.16,1,0.3,1);
    will-change: opacity, transform;
}
.apk-slide.active {
    opacity: 1;
    transform: scale(1) translateX(0);
}
.apk-slide.exit {
    opacity: 0;
    transform: scale(0.97) translateX(-20px);
    transition: opacity 0.45s ease-in,
                transform 0.45s ease-in;
}

/* ── Download button shine ── */
.apk-shine::before {
    content:'';position:absolute;top:0;left:-80%;width:60%;height:100%;
    background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.12) 50%,transparent 60%);
    transition:left .6s ease;
}
.apk-dl-btn:hover .apk-shine::before { left:130%; }

/* ── Modal animations ── */
@keyframes apkSpin    { to{transform:rotate(360deg)} }
@keyframes apkSpinRev { to{transform:rotate(-360deg)} }
@keyframes apkDot     { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
@keyframes apkCircleIn{ 0%{stroke-dashoffset:180} 100%{stroke-dashoffset:0} }
@keyframes apkCheckIn { 0%{stroke-dashoffset:60;opacity:0} 100%{stroke-dashoffset:0;opacity:1} }
</style>

<script>
var APK_URL = '{{ $apkDownloadUrl }}';

// ── Slider dengan smooth transition ──
var apkCurrent = 0;
var apkSlides  = document.querySelectorAll('.apk-slide');
var apkDots    = document.querySelectorAll('.apk-dot');
var apkTimer;

// Set slide pertama active
apkSlides[0].classList.add('active');

function apkGoTo(idx) {
    if (idx === apkCurrent) return;
    var prev = apkCurrent;
    apkCurrent = (idx + apkSlides.length) % apkSlides.length;

    // Exit animation untuk slide lama
    apkSlides[prev].classList.add('exit');
    apkSlides[prev].classList.remove('active');
    setTimeout(function(){ apkSlides[prev].classList.remove('exit'); }, 500);

    // Enter animation untuk slide baru
    apkSlides[apkCurrent].classList.add('active');

    // Update dots
    apkDots[prev].style.width = '4px';
    apkDots[prev].style.background = 'rgba(255,255,255,0.3)';
    apkDots[prev].style.boxShadow = 'none';
    apkDots[apkCurrent].style.width = '20px';
    apkDots[apkCurrent].style.background = 'rgba(255,255,255,0.95)';
    apkDots[apkCurrent].style.boxShadow = '0 0 6px rgba(255,255,255,0.5)';
}

function apkStartAuto() {
    clearInterval(apkTimer);
    apkTimer = setInterval(function(){ apkGoTo(apkCurrent + 1); }, 3000);
}

apkDots.forEach(function(d){ d.addEventListener('click', function(){ apkGoTo(+d.dataset.idx); apkStartAuto(); }); });
apkStartAuto();

// ── Touch swipe untuk mobile ──
var apkSliderEl = document.getElementById('apk-slider');
var apkTouchX = null;
apkSliderEl.addEventListener('touchstart', function(e){ apkTouchX = e.touches[0].clientX; }, {passive:true});
apkSliderEl.addEventListener('touchend', function(e){
    if (apkTouchX === null) return;
    var dx = e.changedTouches[0].clientX - apkTouchX;
    if (Math.abs(dx) > 40) { apkGoTo(dx < 0 ? apkCurrent+1 : apkCurrent-1); apkStartAuto(); }
    apkTouchX = null;
}, {passive:true});

// ── Modal ──
function showApkModal(type) {
    document.getElementById('apk-panel-soon').style.display    = 'none';
    document.getElementById('apk-panel-loading').style.display = 'none';
    document.getElementById('apk-panel-done').style.display    = 'none';

    var modal = document.getElementById('apk-modal');
    var box   = document.getElementById('apk-modal-box');
    modal.style.display = 'flex';

    if (type === 'soon')    document.getElementById('apk-panel-soon').style.display    = 'block';
    if (type === 'loading') document.getElementById('apk-panel-loading').style.display = 'block';
    if (type === 'done')  { document.getElementById('apk-panel-done').style.display    = 'block'; if(window.lucide) lucide.createIcons(); }

    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            box.style.transform = 'translateY(0) scale(1)';
            box.style.opacity   = '1';
            if(window.lucide) lucide.createIcons();
        });
    });
}

function closeApkModal() {
    var box = document.getElementById('apk-modal-box');
    box.style.transform = 'translateY(20px) scale(0.96)';
    box.style.opacity   = '0';
    setTimeout(function(){
        document.getElementById('apk-modal').style.display = 'none';
        box.style.transition = '';
    }, 280);
}

document.getElementById('apk-modal').addEventListener('click', function(e){ if(e.target===this) closeApkModal(); });

document.getElementById('apk-btn').addEventListener('click', function(){
    if (!APK_URL || APK_URL === '') {
        showApkModal('soon');
    } else {
        showApkModal('loading');
        setTimeout(function(){
            var a = document.createElement('a');
            a.href = APK_URL;
            a.download = '{{ $apkSetting->apk_name ? str_replace(" ","-",$apkSetting->apk_name) : "ICB-CT-Presensi" }}.apk';
            document.body.appendChild(a);
            a.click(); a.remove();
            showApkModal('done');
        }, 1800);
    }
});

document.addEventListener('DOMContentLoaded', function(){
    if(window.lucide) lucide.createIcons();
});
</script>
@endsection
