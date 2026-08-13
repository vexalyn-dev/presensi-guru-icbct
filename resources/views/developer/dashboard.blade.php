@extends('layouts.developer')
@section('page-title', 'Dashboard')
@section('content')
<div class="space-y-6">

    {{-- ═══ SECTION: DASHBOARD ═══ --}}
    <div id="sec-dashboard">

        {{-- Welcome Card — identik dashboard.blade.php tapi purple --}}
        <div class="p-6 bg-gradient-to-r from-purple-900 via-purple-800 to-slate-900 dark:from-purple-950 dark:via-purple-900 dark:to-slate-950 rounded-2xl text-white relative overflow-hidden group mb-6">
            <div class="absolute top-0 right-0 w-80 h-80 bg-purple-400/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl group-hover:bg-purple-400/15 transition-all duration-700"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-400/5 rounded-full translate-y-1/3 -translate-x-1/4 blur-2xl group-hover:bg-purple-400/10 transition-all duration-700"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-700/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl flex items-center justify-center shadow-xl shadow-purple-900/40 border border-purple-500/30">
                            <i data-lucide="code-2" class="w-8 h-8 text-white"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h2 class="text-2xl font-bold text-white">Selamat Datang, Vio! 👋</h2>
                                <span class="w-2.5 h-2.5 bg-green-500 rounded-full flex-shrink-0 animate-pulse" style="display:inline-block!important"></span>
                            </div>
                            <p class="text-purple-200/80 text-sm">Developer Dashboard · ICB CT Presensi Guru</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs text-purple-300/70">Hari Ini</p>
                            <p class="text-sm font-bold text-white">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-400/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-purple-400/30 shadow-lg">
                            <i data-lucide="terminal" class="w-6 h-6 text-purple-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards — identik dashboard.blade.php tapi purple --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            @foreach([
                ['Total User',  $stats['total_users'],    'users',          'from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20',   'text-blue-600 dark:text-blue-400'],
                ['Guru',        $stats['total_teachers'], 'graduation-cap', 'from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20','text-green-600 dark:text-green-400'],
                ['Operator',    $stats['total_operators'],'shield',         'from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20','text-purple-600 dark:text-purple-400'],
                ['Pending Izin',$stats['pending_leaves'], 'clock',          'from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/20', 'text-amber-600 dark:text-amber-400'],
            ] as [$l,$v,$ic,$bg,$tc])
            <div class="card p-5 group hover:shadow-lg transition-all hover:-translate-y-0.5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br {{ $bg }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="{{ $ic }}" class="w-6 h-6 {{ $tc }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $l }}</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $v }}</h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Banner Slide Developer --}}
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-lg mb-6" style="height:210px">
            <div class="dslide active absolute inset-0 overflow-hidden" style="background:linear-gradient(135deg,#0D0618 0%,#160D2E 45%,#2A1455 100%)">
                <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full blur-3xl" style="background:rgba(139,92,246,0.12)"></div>
                <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full blur-3xl" style="background:rgba(168,85,247,0.08)"></div>
                <div class="relative z-10 h-full flex items-center justify-between px-6 sm:px-12">
                    <div class="max-w-[220px] sm:max-w-sm">
                        <div class="inline-flex items-center gap-1.5 mb-3 sm:mb-4">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(139,92,246,0.25);border:1px solid rgba(139,92,246,0.45)">
                                <i data-lucide="code-2" class="w-2.5 h-2.5 sm:w-3 sm:h-3" style="color:#C4B5FD"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold tracking-wide uppercase" style="color:#C4B5FD;letter-spacing:.06em">Developer</span>
                        </div>
                        <div class="flex items-center gap-2.5 mb-2 sm:mb-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(139,92,246,0.25);border:1px solid rgba(139,92,246,0.4)">
                                <img src="{{ asset('images/logo-dev-banner.png') }}" alt="Vexalyn Dev" class="w-5 h-5 sm:w-7 sm:h-7 object-contain" style="filter:invert(1) brightness(2)">
                            </div>
                            <div>
                                <p class="text-sm sm:text-base font-extrabold leading-none" style="color:#E9D5FF">Vexalyn Dev</p>
                                <p class="text-[9px] sm:text-[10px] mt-0.5" style="color:rgba(196,181,253,0.6)">vexalyndev.my.id</p>
                            </div>
                        </div>
                        <p class="text-[10px] sm:text-xs leading-relaxed mb-3 sm:mb-4" style="color:rgba(255,255,255,0.5)">Vio Atmajaya Saputra — Developer ICB CT Presensi Guru</p>
                        <a href="https://vexalyndev.my.id" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold transition-all hover:-translate-y-0.5 active:scale-95"
                           style="background:rgba(139,92,246,0.25);border:1px solid rgba(139,92,246,0.45);color:#DDD6FE">
                            <i data-lucide="external-link" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i> Cek Profile Developer
                        </a>
                    </div>
                    <div class="hidden sm:flex items-center justify-center w-36 h-36 md:w-44 md:h-44 flex-shrink-0">
                        <svg viewBox="0 0 180 180" fill="none" class="w-full h-full opacity-75">
                            <rect x="40" y="50" width="100" height="80" rx="10" fill="#8B5CF6" fill-opacity=".12" stroke="#8B5CF6" stroke-width="2.2"/>
                            <path d="M60 70 L72 80 L60 90" stroke="#A78BFA" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M120 70 L108 80 L120 90" stroke="#A78BFA" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M100 67 L80 93" stroke="#C4B5FD" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="90" cy="38" r="15" fill="#8B5CF6" fill-opacity=".18" stroke="#8B5CF6" stroke-width="2"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-1.5" style="background:rgba(255,255,255,.1);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.15);border-radius:99px;padding:5px 8px">
                <div style="width:20px;height:4px;border-radius:99px;background:rgba(255,255,255,.9);box-shadow:0 0 6px rgba(255,255,255,.5)"></div>
            </div>
        </div>

        {{-- System Info + Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                    <i data-lucide="server" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">System Info</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['PHP',    $stats['php_version']],
                        ['Laravel',$stats['laravel_version']],
                        ['Env',    strtoupper($stats['env'])],
                        ['Debug',  $stats['debug']?'ON':'OFF'],
                        ['URL',    parse_url($stats['app_url'],PHP_URL_HOST)??'-'],
                        ['Time',   now()->format('H:i').' WIB'],
                    ] as [$k,$v])
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ $k }}</p>
                        <p class="text-sm font-bold text-purple-700 dark:text-purple-400 truncate">{{ $v }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                    <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">Quick Actions</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        [route('developer.clear-cache',$secret),'refresh-cw','text-teal-600','Clear Cache','confirm("Clear cache?")'],
                        [url('/run-migrate-secret?key=vexalyn19052009'),'database','text-blue-600','Run Migrate',null],
                        [url('/dashboard'),'layout-dashboard','text-purple-600','Dashboard App',null],
                        ['https://app.clickup.com','check-square','text-purple-500','ClickUp',null],
                        ['https://github.com/vexalyn-dev','github','text-slate-600','GitHub',null],
                    ] as [$href,$ic,$c,$lbl,$conf])
                    <a href="{{ $href }}" {{ $conf?"onclick=\"return $conf\"":'' }} target="{{ str_starts_with($href,'http')?'_blank':'' }}"
                       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-400 hover:border-purple-200 dark:hover:border-purple-800/40 transition-all">
                        <i data-lucide="{{ $ic }}" class="w-3.5 h-3.5 {{ $c }}"></i> {{ $lbl }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ SECTION: APK ═══ --}}
    <div id="sec-apk" class="hidden space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-800 to-purple-600 rounded-xl flex items-center justify-center shadow-md shadow-purple-400/20">
                <i data-lucide="smartphone" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-800 dark:text-white">APK Management</h2>
                <p class="text-xs text-slate-400">Upload & kelola APK mobile ICB CT</p>
            </div>
        </div>

        @if($appSetting?->apk_file)
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/40">
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="package-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-green-800 dark:text-green-300">APK Terpasang</p>
                <p class="text-xs text-green-600 dark:text-green-400 truncate">{{ $appSetting->apk_name ?? 'ICB CT Presensi' }} · {{ $appSetting->apk_version_label ?? 'v1.0.0' }} · {{ $appSetting->apk_size_human ?? '-' }}</p>
                @if($appSetting->apk_uploaded_at)<p class="text-[10px] text-green-500/70 mt-0.5">Diupload {{ $appSetting->apk_uploaded_at->diffForHumans() }}</p>@endif
            </div>
            <form action="{{ route('developer.apk.delete',$secret) }}" method="POST" onsubmit="return confirm('Hapus APK?')">
                @csrf @method('DELETE')
                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 hover:bg-red-200 text-red-600 dark:bg-red-900/30 dark:text-red-400 transition-colors">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </form>
        </div>
        @else
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700">
            <i data-lucide="package-x" class="w-5 h-5 text-slate-400 flex-shrink-0"></i>
            <p class="text-sm text-slate-400">Belum ada APK yang diupload.</p>
        </div>
        @endif

        <form action="{{ route('developer.apk',$secret) }}" method="POST" enctype="multipart/form-data"
              class="card p-6 space-y-5">
            @csrf
            <div id="apkZone" onclick="document.getElementById('apkFile').click()"
                 class="relative border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-purple-400 rounded-2xl p-7 text-center transition-colors cursor-pointer"
                 ondragover="event.preventDefault();this.classList.add('border-purple-500','bg-purple-50','dark:bg-purple-900/10')"
                 ondragleave="this.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-900/10')"
                 ondrop="event.preventDefault();this.classList.remove('border-purple-500');handleApk(event.dataTransfer.files[0])">
                <input type="file" name="apk_file" accept=".apk" id="apkFile" class="hidden" onchange="handleApk(this.files[0])">
                <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2.5"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400" id="apkZoneTxt">Drag & drop atau klik untuk pilih .apk</p>
                <p class="text-xs text-slate-400 mt-1">Format: .apk · Maks 100MB — metadata auto-detect</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['apk_name','apkName','type','Nama Aplikasi','ICB CT Presensi'],
                    ['apk_version','apkVer','tag','Versi','1.0.0'],
                    ['apk_min_android','apkAndroid','smartphone','Min. Android','Android 8.0+'],
                    ['apk_changelog','apkLog','file-text','Changelog','Perubahan...'],
                ] as [$n,$id,$ic,$lbl,$ph])
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">{{ $lbl }}</label>
                    <div class="relative">
                        <i data-lucide="{{ $ic }}" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"></i>
                        <input type="text" name="{{ $n }}" id="{{ $id }}" placeholder="{{ $ph }}" value="{{ old($n, $appSetting?->$n ?? '') }}"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-800 to-purple-600 hover:from-purple-900 hover:to-purple-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-purple-400/20 hover:-translate-y-0.5 active:scale-95">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan APK
                </button>
                @if($appSetting?->apk_url)
                <a href="{{ $appSetting->apk_url }}" target="_blank"
                   class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-semibold transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i> Download
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ═══ SECTION: MAINTENANCE ═══ --}}
    <div id="sec-maint" class="hidden space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-400 rounded-xl flex items-center justify-center shadow-md shadow-amber-400/20">
                <i data-lucide="construction" class="w-5 h-5 text-white"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-extrabold text-slate-800 dark:text-white">Mode Maintenance</h2>
                <p class="text-xs text-slate-400">Tampilkan halaman maintenance ke guru — admin tetap bisa akses</p>
            </div>
            @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $mOn ? 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/40' : 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800/40' }}">
                {{ $mOn ? '● AKTIF' : '○ NONAKTIF' }}
            </span>
        </div>
        <form action="{{ route('developer.maintenance',$secret) }}" method="POST" class="card p-6 space-y-5">
            @csrf
            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Aktifkan Maintenance Mode</p>
                    <p class="text-xs text-slate-400 mt-0.5">Admin & Operator tetap bisa login dan akses semua fitur</p>
                </div>
                <label class="cursor-pointer" onclick="toggleMaintUI()">
                    <input type="hidden" name="maintenance_mode" id="maintVal" value="{{ $mOn?'1':'0' }}">
                    <div id="maintTrack" class="relative w-11 h-6 rounded-full transition-colors duration-300 {{ $mOn?'bg-red-500':'bg-slate-300 dark:bg-slate-600' }}">
                        <div id="maintThumb" class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-300 {{ $mOn?'left-[22px]':'left-0.5' }}"></div>
                    </div>
                </label>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Pesan Maintenance</label>
                <textarea name="maintenance_message" rows="2" placeholder="Sistem sedang dalam pemeliharaan…"
                          class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
            </div>
            <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-800 to-purple-600 hover:from-purple-900 hover:to-purple-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-purple-400/20 hover:-translate-y-0.5 active:scale-95">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Status
            </button>
        </form>
    </div>

    {{-- ═══ SECTION: UPDATES ═══ --}}
    <div id="sec-updates" class="hidden space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-sky-600 to-sky-400 rounded-xl flex items-center justify-center shadow-md shadow-sky-400/20">
                <i data-lucide="rocket" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-800 dark:text-white">Rilis Update</h2>
                <p class="text-xs text-slate-400">Tulis changelog — tampil ke user lewat modal sambutan</p>
            </div>
        </div>

        <form action="{{ route('developer.updates.store',$secret) }}" method="POST" class="card p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Versi</label>
                    <input type="text" name="version" placeholder="2.3.1" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tipe</label>
                    <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition cursor-pointer">
                        <option value="feature">✨ Feature</option>
                        <option value="fix">🔧 Fix</option>
                        <option value="update">⬆️ Update</option>
                        <option value="hotfix">🚨 Hotfix</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Judul Update</label>
                <input type="text" name="title" placeholder="Perbaikan bug + fitur baru" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Detail Perubahan</label>
                <textarea name="content" rows="4" placeholder="• Perbaiki bug&#10;• Tambah fitur baru&#10;• Update UI" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-y" required></textarea>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_modal" value="1" checked class="w-4 h-4 rounded focus:ring-purple-500" style="accent-color:#7c3aed">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Tampilkan ke user lewat modal</span>
                </label>
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-800 to-purple-600 hover:from-purple-900 hover:to-purple-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-purple-400/20 hover:-translate-y-0.5 active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah
                </button>
            </div>
        </form>

        @if(count($updates) > 0)
        <div class="space-y-3">
            @foreach($updates as $u)
            @php $tc = match($u->type) {
                'feature' => ['text-purple-700 dark:text-purple-400','bg-purple-100 dark:bg-purple-900/20','border-purple-200 dark:border-purple-800/40'],
                'fix'     => ['text-green-700 dark:text-green-400','bg-green-100 dark:bg-green-900/20','border-green-200 dark:border-green-800/40'],
                'hotfix'  => ['text-red-600 dark:text-red-400','bg-red-100 dark:bg-red-900/20','border-red-200 dark:border-red-800/40'],
                default   => ['text-blue-600 dark:text-blue-400','bg-blue-100 dark:bg-blue-900/20','border-blue-200 dark:border-blue-800/40'],
            }; @endphp
            <div class="card flex items-start gap-3 p-4 hover:shadow-md transition-all">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase flex-shrink-0 mt-0.5 {{ $tc[0] }} {{ $tc[1] }} border {{ $tc[2] }}">{{ $u->type }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="text-sm font-bold text-slate-800 dark:text-white">v{{ $u->version }}</span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ $u->title }}</span>
                        @if($u->show_modal)<span class="text-[10px] text-purple-700 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/20 px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-800/40">modal</span>@endif
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ \Illuminate\Support\Str::limit($u->content, 80) }} · {{ $u->created_at->diffForHumans() }}</p>
                </div>
                <form action="{{ route('developer.updates.delete',[$secret,$u->id]) }}" method="POST" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors flex-shrink-0">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="card p-10 text-center">
            <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
            <p class="text-sm text-slate-400">Belum ada update.</p>
        </div>
        @endif
    </div>

</div>
@endsection
