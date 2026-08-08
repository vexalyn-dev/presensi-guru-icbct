@extends(activeLayout())
@section('page-title', 'Download Aplikasi')
@section('content')

{{-- ════════════════════════════════════════════════════════ --}}
{{--  BANNER SLIDER                                          --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="relative -mx-6 -mt-6 overflow-hidden rounded-b-3xl" style="height:280px;" id="apk-slider">

    {{-- Slide 1: Presensi Mudah --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-10 sm:px-16"
         style="background:linear-gradient(135deg,#0A1628 0%,#0F2847 50%,#1a3a5c 100%);">
        <div class="z-10 max-w-xs sm:max-w-md">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-400/15 text-sky-300 text-xs font-bold mb-3 border border-sky-400/30 backdrop-blur-sm">
                <i data-lucide="sparkles" class="w-3 h-3"></i> Aplikasi Resmi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-2">Presensi Guru<br><span class="text-sky-400">Lebih Mudah</span></h2>
            <p class="text-sm text-white/60 leading-relaxed">Scan QR, validasi GPS, semua bisa dari HP kamu kapan aja.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-44 h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full opacity-85">
                <rect x="50" y="20" width="80" height="140" rx="16" fill="#38BDF8" fill-opacity=".12" stroke="#38BDF8" stroke-width="2.5"/>
                <rect x="62" y="36" width="56" height="88" rx="6" fill="#0F172A"/>
                <circle cx="90" cy="142" r="6" fill="#38BDF8" fill-opacity=".7"/>
                <rect x="74" y="28" width="32" height="4" rx="2" fill="#38BDF8" fill-opacity=".45"/>
                <rect x="68" y="50" width="44" height="6" rx="3" fill="#38BDF8" fill-opacity=".3"/>
                <rect x="68" y="62" width="30" height="4" rx="2" fill="white" fill-opacity=".2"/>
                <rect x="68" y="72" width="38" height="4" rx="2" fill="white" fill-opacity=".15"/>
                <circle cx="90" cy="95" r="16" fill="#38BDF8" fill-opacity=".18" stroke="#38BDF8" stroke-width="1.8"/>
                <path d="M84 95l4 4 8-8" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-sky-400/8 blur-3xl"></div>
    </div>

    {{-- Slide 2: GPS Validasi --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-10 sm:px-16"
         style="background:linear-gradient(135deg,#0A1F0E 0%,#0D3E1F 50%,#155D30 100%);">
        <div class="z-10 max-w-xs sm:max-w-md">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-400/15 text-emerald-300 text-xs font-bold mb-3 border border-emerald-400/30 backdrop-blur-sm">
                <i data-lucide="map-pin" class="w-3 h-3"></i> GPS Validasi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-2">Absensi Hanya<br><span class="text-emerald-400">Di Area Sekolah</span></h2>
            <p class="text-sm text-white/60 leading-relaxed">Sistem otomatis cek lokasi kamu sebelum absensi diproses.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-44 h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full opacity-85">
                <circle cx="90" cy="75" r="42" fill="#10B981" fill-opacity=".08" stroke="#10B981" stroke-width="2"/>
                <circle cx="90" cy="75" r="26" fill="#10B981" fill-opacity=".15" stroke="#10B981" stroke-width="2"/>
                <circle cx="90" cy="75" r="11" fill="#10B981" fill-opacity=".45"/>
                <path d="M90 75v50" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" stroke-dasharray="4 4"/>
                <ellipse cx="90" cy="128" rx="20" ry="7" fill="#10B981" fill-opacity=".22"/>
                <path d="M58 132 Q68 110 90 125 Q112 110 122 132" stroke="#10B981" stroke-width="1.6" fill="none" opacity=".45"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -top-20 -left-20 w-72 h-72 rounded-full bg-emerald-400/8 blur-3xl"></div>
    </div>

    {{-- Slide 3: Notifikasi Real-time --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-10 sm:px-16"
         style="background:linear-gradient(135deg,#1A0A28 0%,#2D1842 50%,#44265D 100%);">
        <div class="z-10 max-w-xs sm:max-w-md">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-400/15 text-purple-300 text-xs font-bold mb-3 border border-purple-400/30 backdrop-blur-sm">
                <i data-lucide="bell" class="w-3 h-3"></i> Real-time
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-2">Notifikasi<br><span class="text-purple-400">Langsung Masuk</span></h2>
            <p class="text-sm text-white/60 leading-relaxed">Jadwal, pengumuman & izin semua langsung muncul di HP kamu.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-44 h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full opacity-85">
                <path d="M90 30 C65 30 48 50 48 72 L48 110 L36 122 L144 122 L132 110 L132 72 C132 50 115 30 90 30Z" fill="#A855F7" fill-opacity=".15" stroke="#A855F7" stroke-width="2.2"/>
                <path d="M82 122 Q82 132 90 132 Q98 132 98 122" stroke="#A855F7" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                <circle cx="90" cy="30" r="6" fill="#A855F7" fill-opacity=".5"/>
                <circle cx="126" cy="46" r="13" fill="#A855F7" fill-opacity=".88"/>
                <path d="M121 46h5m-2.5-2.5v5" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                <rect x="62" y="78" width="56" height="7" rx="3.5" fill="#A855F7" fill-opacity=".28"/>
                <rect x="62" y="92" width="40" height="5" rx="2.5" fill="#A855F7" fill-opacity=".18"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-purple-400/8 blur-3xl"></div>
    </div>

    {{-- Slide 4: Developer Info --}}
    <div class="apk-slide absolute inset-0 flex items-center justify-between px-10 sm:px-16"
         style="background:linear-gradient(135deg,#1E0A28 0%,#3D1A4F 50%,#5C2A76 100%);">
        <div class="z-10 max-w-xs sm:max-w-md">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-fuchsia-400/15 text-fuchsia-300 text-xs font-bold mb-3 border border-fuchsia-400/30 backdrop-blur-sm">
                <i data-lucide="code-2" class="w-3 h-3"></i> Developer
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-2">Dikembangkan Oleh<br><span class="text-fuchsia-400">Vexalyn Dev</span></h2>
            <p class="text-sm text-white/60 leading-relaxed mb-4">Tim developer berpengalaman yang fokus membangun solusi digital untuk pendidikan.</p>
            <a href="https://vexalyn.dev" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-fuchsia-500/20 hover:bg-fuchsia-500/30 border border-fuchsia-400/40 text-fuchsia-200 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5">
                <i data-lucide="external-link" class="w-4 h-4"></i> Cek Profile Developer
            </a>
        </div>
        <div class="hidden sm:flex items-center justify-center w-44 h-44 flex-shrink-0">
            <svg viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full opacity-85">
                <rect x="40" y="50" width="100" height="80" rx="8" fill="#C026D3" fill-opacity=".12" stroke="#C026D3" stroke-width="2.2"/>
                <path d="M60 70 L70 80 L60 90" stroke="#C026D3" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M120 70 L110 80 L120 90" stroke="#C026D3" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M100 68 L80 92" stroke="#C026D3" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="90" cy="40" r="16" fill="#C026D3" fill-opacity=".2" stroke="#C026D3" stroke-width="2"/>
                <path d="M90 32v16m-6-10l6-6 6 6" stroke="#C026D3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="pointer-events-none absolute -top-20 -right-20 w-72 h-72 rounded-full bg-fuchsia-400/8 blur-3xl"></div>
    </div>

    {{-- Dots --}}
    <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        <button class="apk-dot w-6 h-1.5 rounded-full bg-white/90 transition-all shadow-sm" data-idx="0"></button>
        <button class="apk-dot w-2 h-1.5 rounded-full bg-white/30 transition-all" data-idx="1"></button>
        <button class="apk-dot w-2 h-1.5 rounded-full bg-white/30 transition-all" data-idx="2"></button>
        <button class="apk-dot w-2 h-1.5 rounded-full bg-white/30 transition-all" data-idx="3"></button>
    </div>

    {{-- Prev / Next arrows --}}
    <button id="apk-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
        <i data-lucide="chevron-left" class="w-4 h-4 text-white"></i>
    </button>
    <button id="apk-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
        <i data-lucide="chevron-right" class="w-4 h-4 text-white"></i>
    </button>
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
                <h1 class="text-2xl font-extrabold text-navy-800 dark:text-white leading-tight">ICB CT <span class="text-gold-500">Presensi</span></h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Presensi Guru — SMK ICB Cinta Teknika</p>
                <div class="flex gap-2 mt-1.5 flex-wrap">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">v1.0.0</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">Android 8.0+</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">~12 MB</span>
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
                        <p class="text-[9px] text-slate-400 text-center leading-tight">Ukuran kecil ~12 MB</p>
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
                <div class="flex items-center justify-center gap-2 mt-4 text-[10px] text-slate-400">
                    <span>Dikembangkan oleh</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gradient-to-r from-fuchsia-500/10 to-purple-500/10 border border-fuchsia-400/20 text-fuchsia-600 dark:text-fuchsia-400 font-bold tracking-tight">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        Vexalyn Dev
                    </span>
                    <span>· v1.0.0</span>
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

        {{-- Panel: Belum tersedia --}}
        <div id="apk-panel-soon" class="p-8 text-center">
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="construction" class="w-8 h-8 text-amber-500"></i>
            </div>
            <h3 class="text-lg font-extrabold text-navy-800 dark:text-white mb-2">Bentar dulu ya! 🙏</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-2">APK-nya lagi kita siapkan biar mantap banget sebelum rilis.</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">Nantikan update-nya, ya! <span class="font-semibold text-navy-800 dark:text-slate-200">Gak bakal lama kok 🚀</span></p>
            <button onclick="closeApkModal()" class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 active:scale-95 transition-all shadow-lg">Siap, ditunggu!</button>
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
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-2">File APK udah mulai diunduh ke perangkat lo.</p>
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
}

/* ── Slider smooth transitions ── */
.apk-slide {
    opacity: 0;
    transform: translateX(20px);
    transition: opacity 0.6s cubic-bezier(0.22,1,0.36,1), transform 0.6s cubic-bezier(0.22,1,0.36,1);
}
.apk-slide.active {
    opacity: 1;
    transform: translateX(0);
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
var APK_URL = '{{ env("APK_DOWNLOAD_URL","") }}';

// ── Slider dengan smooth transition ──
var apkCurrent = 0;
var apkSlides  = document.querySelectorAll('.apk-slide');
var apkDots    = document.querySelectorAll('.apk-dot');
var apkTimer;

// Set slide pertama active
apkSlides[0].classList.add('active');

function apkGoTo(idx) {
    apkSlides[apkCurrent].classList.remove('active');
    apkDots[apkCurrent].classList.remove('w-6','bg-white/90','shadow-sm');
    apkDots[apkCurrent].classList.add('w-2','bg-white/30');

    apkCurrent = (idx + apkSlides.length) % apkSlides.length;

    apkSlides[apkCurrent].classList.add('active');
    apkDots[apkCurrent].classList.remove('w-2','bg-white/30');
    apkDots[apkCurrent].classList.add('w-6','bg-white/90','shadow-sm');
}

function apkStartAuto() {
    clearInterval(apkTimer);
    apkTimer = setInterval(function(){ apkGoTo(apkCurrent + 1); }, 5000);
}

document.getElementById('apk-next').addEventListener('click', function(){ apkGoTo(apkCurrent+1); apkStartAuto(); });
document.getElementById('apk-prev').addEventListener('click', function(){ apkGoTo(apkCurrent-1); apkStartAuto(); });
apkDots.forEach(function(d){ d.addEventListener('click', function(){ apkGoTo(+d.dataset.idx); apkStartAuto(); }); });
apkStartAuto();

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
            a.download = 'ICB-CT-Presensi.apk';
            document.body.appendChild(a);
            a.click(); a.remove();
            showApkModal('done');
        }, 2000);
    }
});

document.addEventListener('DOMContentLoaded', function(){
    if(window.lucide) lucide.createIcons();
});
</script>
@endsection
