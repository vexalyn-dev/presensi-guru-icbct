<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dev Panel · ICB CT</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
[x-cloak]{display:none!important}
body{font-family:'Inter',sans-serif}
.nav-item{position:relative;overflow:hidden;transition:background .22s ease,color .22s ease,transform .18s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease}
.nav-item::before{content:'';position:absolute;top:0;left:-120%;width:60%;height:100%;background:linear-gradient(105deg,transparent,rgba(255,255,255,.07),transparent);transition:left .55s cubic-bezier(.4,0,.2,1);pointer-events:none;z-index:0}
.nav-item:hover::before{left:160%}
.nav-item::after{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;border-radius:0 3px 3px 0;background:rgba(167,139,250,.7);transform:scaleY(0);transform-origin:center;transition:transform .2s cubic-bezier(.34,1.56,.64,1)}
.nav-item:hover::after{transform:scaleY(1)}
.nav-item:hover:not(.nav-active){transform:translateX(3px)}
.nav-item.nav-active::after{display:none}
.fade-in{animation:fadeIn .4s ease-out}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.slide-up{animation:slideUp .4s ease-out}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
/* Dev banner slider */
.dslide{position:absolute;inset:0;opacity:0;transform:translateX(28px);transition:opacity .65s cubic-bezier(.16,1,.3,1),transform .65s cubic-bezier(.16,1,.3,1)}
.dslide.active{opacity:1;transform:translateX(0)}
.dslide.exit{opacity:0;transform:translateX(-20px);transition:opacity .4s ease-in,transform .4s ease-in}
/* Loading */
@keyframes spin{to{transform:rotate(360deg)}}
/* Welcome card orb */
.dev-orb{position:absolute;border-radius:50%;filter:blur(55px);pointer-events:none;animation:devOrb 7s ease-in-out infinite}
@keyframes devOrb{0%,100%{opacity:.35}50%{opacity:.65}}
</style>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300"
      x-data="{
          sidebarOpen: false,
          darkMode: (() => { try { return localStorage.getItem('devTheme') === 'dark'; } catch(e){ return false; } })(),
          spinAnim: false
      }"
      :class="{ 'dark': darkMode }"
      x-init="
          $watch('darkMode', val => {
              try { localStorage.setItem('devTheme', val ? 'dark' : 'light'); } catch(e){}
              if(val) document.documentElement.classList.add('dark');
              else document.documentElement.classList.remove('dark');
          });
          if(darkMode) document.documentElement.classList.add('dark');
      ">

{{-- ══ LOADING SCREEN ══ --}}
<div id="devLoader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center gap-5 bg-slate-50 dark:bg-slate-950 transition-opacity duration-500">
    <div class="relative w-16 h-16">
        <div style="position:absolute;inset:0;border-radius:50%;border:3px solid #e9d5ff;border-top-color:#7c3aed;animation:spin .9s linear infinite"></div>
        <div style="position:absolute;top:9px;left:9px;right:9px;bottom:9px;border-radius:50%;border:3px solid transparent;border-bottom-color:#a855f7;animation:spin .7s linear infinite reverse"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <i data-lucide="code-2" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
        </div>
    </div>
    <div class="text-center">
        <p class="text-sm font-bold text-slate-800 dark:text-white" id="loaderMsg">Memuat Dev Panel…</p>
        <p class="text-xs text-slate-400 mt-1">ICB CT · Vexalyn Dev</p>
    </div>
    <div class="w-48 h-1.5 bg-purple-100 dark:bg-purple-900/30 rounded-full overflow-hidden">
        <div id="loaderBar" class="h-full bg-gradient-to-r from-purple-700 to-purple-400 rounded-full" style="width:0;transition:width .08s linear"></div>
    </div>
</div>

{{-- ══ WELCOME MODAL ══ --}}
<div id="devModal" class="fixed inset-0 z-[8888] bg-black/55 backdrop-blur-sm hidden items-center justify-center p-4">
    <div id="devModalBox" class="bg-white dark:bg-slate-800 rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl shadow-purple-900/20 border border-slate-100 dark:border-slate-700"
         style="transform:translateY(30px) scale(.95);opacity:0;transition:all .45s cubic-bezier(.16,1,.3,1)">
        <div class="h-1.5 bg-gradient-to-r from-purple-800 via-purple-500 to-violet-400"></div>
        <div class="p-8">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-800 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-purple-400/30">
                <i data-lucide="code-2" class="w-7 h-7 text-white"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white text-center mb-2">Selamat Datang, Developer! 👋</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center leading-relaxed mb-5">
                Kamu mengakses <strong class="text-purple-700 dark:text-purple-400">Developer Dashboard</strong> ICB CT via URL rahasia.
            </p>
            @if($latestUpdate)
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 rounded-2xl p-4 mb-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-800/40 text-purple-700 dark:text-purple-300">{{ $latestUpdate->type }}</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">v{{ $latestUpdate->version }}</span>
                    <span class="text-[10px] text-slate-400 ml-auto">{{ $latestUpdate->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">{{ $latestUpdate->title }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ \Illuminate\Support\Str::limit($latestUpdate->content, 140) }}</p>
            </div>
            @endif
            <button onclick="closeModal()"
                class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-purple-800 to-purple-600 hover:from-purple-900 hover:to-purple-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-purple-400/25 hover:-translate-y-0.5 active:scale-95">
                <i data-lucide="check" class="w-4 h-4"></i> Masuk ke Dashboard
            </button>
        </div>
    </div>
</div>

{{-- Mobile Overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"></div>

{{-- ══ SIDEBAR — persis app.blade.php tapi warna purple ══ --}}
<aside id="devSidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-transform duration-300 ease-out">

    {{-- Logo --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-200 dark:border-slate-800">
        <div class="w-9 h-9 bg-gradient-to-br from-purple-700 to-purple-500 rounded-lg flex items-center justify-center shadow-lg shadow-purple-700/30">
            <i data-lucide="code-2" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <h1 class="font-bold text-base text-slate-800 dark:text-white leading-tight">Dev Panel</h1>
            <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">ICB CT · Vexalyn Dev</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto no-scrollbar">
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Menu</p>
            @foreach([
                ['id'=>'sec-dashboard','icon'=>'layout-dashboard','label'=>'Dashboard'],
                ['id'=>'sec-apk',      'icon'=>'smartphone',      'label'=>'APK Management'],
                ['id'=>'sec-maint',    'icon'=>'construction',     'label'=>'Maintenance Mode'],
                ['id'=>'sec-updates',  'icon'=>'rocket',           'label'=>'Rilis Update'],
            ] as $nav)
            <button onclick="showSection('{{ $nav['id'] }}')" id="nav-{{ $nav['id'] }}"
                class="nav-item w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="{{ $nav['icon'] }}" class="w-4 h-4"></i>
                <span>{{ $nav['label'] }}</span>
            </button>
            @endforeach
        </div>
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Tools</p>
            <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirm('Clear semua cache?')"
               class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-teal-500"></i><span>Clear Cache</span>
            </a>
            <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank"
               class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="database" class="w-4 h-4 text-blue-500"></i><span>Run Migrate</span>
            </a>
            <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank"
               class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="github" class="w-4 h-4"></i><span>GitHub Repo</span>
            </a>
        </div>
    </nav>
</aside>

{{-- ══ MAIN ══ --}}
<div class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300">

    {{-- Topbar — persis app.blade.php --}}
    <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-5 h-16 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = true"
                class="lg:hidden flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-300">
                <i data-lucide="menu" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white" id="pageTitle">Dashboard</h2>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Dark Mode Toggle — sama persis app.blade.php --}}
            <button @click="darkMode = !darkMode; spinAnim = true; setTimeout(() => spinAnim = false, 500)"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 cursor-pointer focus:outline-none">
                <i data-lucide="sun" x-show="!darkMode" class="w-5 h-5 text-slate-600 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
                <i data-lucide="moon" x-show="darkMode" x-cloak class="w-5 h-5 text-purple-400 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
            </button>

            {{-- Lang --}}
            <button onclick="toggleLang()"
                class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <span id="langLabel">ID</span>
            </button>

            {{-- Profile dropdown — hanya Profil Developer + Logout --}}
            <div class="relative" x-data="{open:false}" @click.outside="open=false">
                <button @click="open=!open"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                    <div class="w-7 h-7 bg-gradient-to-br from-purple-700 to-purple-500 rounded-lg flex items-center justify-center shadow-sm">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-white"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 hidden sm:block">Vio Atmajaya</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-200 dark:border-slate-700 overflow-hidden z-50 p-2">
                    <div class="px-3 py-2.5 border-b border-slate-100 dark:border-slate-700 mb-1">
                        <p class="text-xs font-bold text-slate-800 dark:text-white">Vio Atmajaya Saputra</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">vexalyndev.my.id</p>
                    </div>
                    <a href="https://vexalyndev.my.id" target="_blank"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-400 transition-all">
                        <i data-lucide="external-link" class="w-4 h-4"></i> Profil Developer
                    </a>
                    <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                    </a>
                </div>
            </div>

            {{-- Secret badge --}}
            <span class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 text-purple-700 dark:text-purple-400 text-[11px] font-bold">
                <i data-lucide="shield-check" class="w-3 h-3"></i> SECRET
            </span>
        </div>
    </header>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto">
        <div class="px-5 py-6 max-w-5xl mx-auto">

{{-- ═══ ALERTS ═══ --}}
@if(session('success'))
<div class="flex items-center gap-3 p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/40 text-green-700 dark:text-green-400 mb-6 fade-in">
    <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
    <span class="text-sm font-semibold">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 mb-6">
    @foreach($errors->all() as $e)<p class="text-xs text-red-600 dark:text-red-400">• {{ $e }}</p>@endforeach
</div>
@endif

{{-- ═══ SEC: DASHBOARD ═══ --}}
<div id="sec-dashboard" class="dev-section space-y-6 fade-in">

    {{-- Welcome Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-950 via-purple-800 to-violet-700 p-7 shadow-xl shadow-purple-900/25">
        <div class="dev-orb w-56 h-56 bg-purple-400/20" style="top:-80px;right:-60px"></div>
        <div class="dev-orb w-36 h-36 bg-violet-400/20" style="bottom:-40px;left:35%;animation-delay:3s"></div>
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-white/70 text-[11px] font-bold mb-4 backdrop-blur-sm">
                    <i data-lucide="shield-check" class="w-3 h-3"></i> DEVELOPER ACCESS
                </div>
                <h1 class="text-3xl font-black text-white leading-tight mb-2">
                    Selamat datang,<br><span class="text-purple-200">Vio Atmajaya</span> 👋
                </h1>
                <p class="text-sm text-white/60">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                @foreach([
                    ['Total User',  $stats['total_users'],    'users'],
                    ['Guru',        $stats['total_teachers'], 'graduation-cap'],
                    ['Operator',    $stats['total_operators'],'shield'],
                    ['Pending Izin',$stats['pending_leaves'], 'clock'],
                ] as [$l,$v,$ic])
                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-3.5 text-center min-w-[80px]">
                    <i data-lucide="{{ $ic }}" class="w-4 h-4 text-white/60 mx-auto mb-1.5 block"></i>
                    <p class="text-2xl font-black text-white leading-none">{{ $v }}</p>
                    <p class="text-[10px] text-white/50 mt-1">{{ $l }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Banner Slider developer --}}
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-lg" style="height:210px">
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
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                <i data-lucide="server" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                <p class="text-sm font-bold text-slate-800 dark:text-white">System Info</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['PHP',     $stats['php_version']],
                    ['Laravel', $stats['laravel_version']],
                    ['Env',     strtoupper($stats['env'])],
                    ['Debug',   $stats['debug']?'ON':'OFF'],
                    ['URL',     parse_url($stats['app_url'],PHP_URL_HOST)??'-'],
                    ['Time',    now()->format('H:i').' WIB'],
                ] as [$k,$v])
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ $k }}</p>
                    <p class="text-sm font-bold text-purple-700 dark:text-purple-400 truncate">{{ $v }}</p>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                <p class="text-sm font-bold text-slate-800 dark:text-white">Quick Actions</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach([
                    [route('developer.clear-cache',$secret),'refresh-cw','text-teal-600','Clear Cache','confirm("Clear cache?")'],
                    [url('/run-migrate-secret?key=vexalyn19052009'),'database','text-blue-600','Run Migrate',null],
                    [url('/dashboard'),'layout-dashboard','text-purple-600','Dashboard App',null],
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

{{-- ═══ SEC: APK ═══ --}}
<div id="sec-apk" class="dev-section hidden space-y-6 fade-in">
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
            @if($appSetting->apk_uploaded_at)
            <p class="text-[10px] text-green-500/70 mt-0.5">Diupload {{ $appSetting->apk_uploaded_at->diffForHumans() }}</p>
            @endif
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
          class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-6 space-y-5 shadow-sm">
        @csrf
        <div id="apkZone" onclick="document.getElementById('apkFile').click()"
             class="relative border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-purple-400 rounded-2xl p-7 text-center transition-colors cursor-pointer"
             ondragover="event.preventDefault();this.classList.add('border-purple-500','bg-purple-50','dark:bg-purple-900/10')"
             ondragleave="this.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-900/10')"
             ondrop="event.preventDefault();this.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-900/10');handleApk(event.dataTransfer.files[0])">
            <input type="file" name="apk_file" accept=".apk" id="apkFile" class="hidden" onchange="handleApk(this.files[0])">
            <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2.5"></i>
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400" id="apkZoneTxt">Drag & drop atau klik untuk pilih .apk</p>
            <p class="text-xs text-slate-400 mt-1">Format: .apk · Maks 100MB — metadata auto-detect</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['apk_name',       'apkName',    'type',       'Nama Aplikasi', 'ICB CT Presensi'],
                ['apk_version',    'apkVer',     'tag',        'Versi',         '1.0.0'],
                ['apk_min_android','apkAndroid', 'smartphone', 'Min. Android',  'Android 8.0+'],
                ['apk_changelog',  'apkLog',     'file-text',  'Changelog',     'Perubahan...'],
            ] as [$n,$id,$ic,$lbl,$ph])
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">{{ $lbl }} <span class="text-[10px] text-slate-400 font-normal normal-case tracking-normal">auto dari APK</span></label>
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

{{-- ═══ SEC: MAINTENANCE ═══ --}}
<div id="sec-maint" class="dev-section hidden space-y-6 fade-in">
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

    <form action="{{ route('developer.maintenance',$secret) }}" method="POST"
          class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-6 space-y-5 shadow-sm">
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

{{-- ═══ SEC: UPDATES ═══ --}}
<div id="sec-updates" class="dev-section hidden space-y-6 fade-in">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-sky-600 to-sky-400 rounded-xl flex items-center justify-center shadow-md shadow-sky-400/20">
            <i data-lucide="rocket" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white">Rilis Update</h2>
            <p class="text-xs text-slate-400">Tulis changelog — tampil ke user lewat modal sambutan</p>
        </div>
    </div>

    <form action="{{ route('developer.updates.store',$secret) }}" method="POST"
          class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Versi</label>
                <input type="text" name="version" placeholder="2.3.1"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
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
            <input type="text" name="title" placeholder="Perbaikan bug + fitur baru"
                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Detail Perubahan</label>
            <textarea name="content" rows="4" placeholder="• Perbaiki bug&#10;• Tambah fitur baru&#10;• Update UI"
                      class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-y" required></textarea>
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
        @php
        $tc = match($u->type) {
            'feature' => ['text-purple-700 dark:text-purple-400','bg-purple-100 dark:bg-purple-900/20','border-purple-200 dark:border-purple-800/40'],
            'fix'     => ['text-green-700 dark:text-green-400','bg-green-100 dark:bg-green-900/20','border-green-200 dark:border-green-800/40'],
            'hotfix'  => ['text-red-600 dark:text-red-400','bg-red-100 dark:bg-red-900/20','border-red-200 dark:border-red-800/40'],
            default   => ['text-blue-600 dark:text-blue-400','bg-blue-100 dark:bg-blue-900/20','border-blue-200 dark:border-blue-800/40'],
        };
        @endphp
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all">
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase flex-shrink-0 mt-0.5 {{ $tc[0] }} {{ $tc[1] }} border {{ $tc[2] }}">{{ $u->type }}</span>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">v{{ $u->version }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $u->title }}</span>
                    @if($u->show_modal)
                    <span class="text-[10px] text-purple-700 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/20 px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-800/40">modal</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500">{{ \Illuminate\Support\Str::limit($u->content, 80) }} · {{ $u->created_at->diffForHumans() }}</p>
            </div>
            <form action="{{ route('developer.updates.delete',[$secret,$u->id]) }}" method="POST" onsubmit="return confirm('Hapus update ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors flex-shrink-0">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-10 text-center shadow-sm">
        <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
        <p class="text-sm text-slate-400">Belum ada update yang ditambahkan.</p>
    </div>
    @endif
</div>

        </div>{{-- end px-5 py-6 --}}
    </div>{{-- end flex-1 overflow-y-auto --}}
</div>{{-- end lg:ml-64 --}}

<script>
// ── Loading ──
const lMsgs=['Memuat Dev Panel…','Menyiapkan data…','Hampir selesai…','Siap! ✓'];
let lp=0;
const lb=document.getElementById('loaderBar');
const lt=setInterval(()=>{
    lp+=Math.random()*22+8; if(lp>100) lp=100;
    lb.style.width=lp+'%';
    document.getElementById('loaderMsg').textContent=lMsgs[Math.floor((lp/100)*(lMsgs.length-1))];
    if(lp>=100){clearInterval(lt);setTimeout(showApp,350);}
},110);

function showApp(){
    const ld=document.getElementById('devLoader');
    ld.style.opacity='0';
    setTimeout(()=>{
        ld.style.display='none';
        if(window.lucide) lucide.createIcons();
        showModal();
    },500);
}

// ── Modal ──
function showModal(){
    const m=document.getElementById('devModal');
    const b=document.getElementById('devModalBox');
    m.classList.remove('hidden');
    m.style.display='flex';
    requestAnimationFrame(()=>requestAnimationFrame(()=>{
        b.style.transform='translateY(0) scale(1)';
        b.style.opacity='1';
        if(window.lucide) lucide.createIcons();
    }));
}
function closeModal(){
    const b=document.getElementById('devModalBox');
    b.style.transform='translateY(20px) scale(.95)';
    b.style.opacity='0';
    setTimeout(()=>{
        document.getElementById('devModal').style.display='none';
        document.getElementById('devModal').classList.add('hidden');
    },350);
}
document.getElementById('devModal').addEventListener('click',e=>{
    if(e.target===document.getElementById('devModal')) closeModal();
});

// ── Section navigation ──
const devSections=['sec-dashboard','sec-apk','sec-maint','sec-updates'];
const devTitles={'sec-dashboard':'Dashboard','sec-apk':'APK Management','sec-maint':'Maintenance Mode','sec-updates':'Rilis Update'};
function showSection(id){
    devSections.forEach(s=>{
        const el=document.getElementById(s);
        if(el) el.classList.toggle('hidden',s!==id);
        const nav=document.getElementById('nav-'+s);
        if(nav){
            if(s===id){
                nav.classList.add('nav-active','bg-purple-700','text-white','shadow-lg');
                nav.classList.remove('text-slate-600','hover:bg-slate-100','dark:text-slate-400','dark:hover:bg-slate-800');
            } else {
                nav.classList.remove('nav-active','bg-purple-700','text-white','shadow-lg');
                nav.classList.add('text-slate-600','hover:bg-slate-100','dark:text-slate-400','dark:hover:bg-slate-800');
            }
        }
    });
    const pt=document.getElementById('pageTitle');
    if(pt) pt.textContent=devTitles[id]||'Dashboard';
    window.scrollTo({top:0,behavior:'smooth'});
}
showSection('sec-dashboard');

// ── Lang toggle ──
let devLang=localStorage.getItem('devLang')||'id';
const devI18n={id:'ID',en:'EN'};
function toggleLang(){devLang=devLang==='id'?'en':'id';localStorage.setItem('devLang',devLang);document.getElementById('langLabel').textContent=devI18n[devLang];}
document.getElementById('langLabel').textContent=devI18n[devLang];

// ── APK auto-fill ──
function handleApk(file){
    if(!file) return;
    document.getElementById('apkZoneTxt').textContent=file.name;
    document.getElementById('apkZone').classList.add('border-purple-500');
    const base=file.name.replace(/\.apk$/i,'');
    const vm=base.match(/[vV]?(\d+\.\d+(?:\.\d+)?(?:\.\d+)?)/);
    if(vm){
        document.getElementById('apkVer').value=vm[1];
        const nm=base.replace(/[-_\s]*[vV]?\d+\.\d+(?:\.\d+)?(?:\.\d+)?[-_\s]*/g,'').replace(/[-_]/g,' ').trim();
        if(nm) document.getElementById('apkName').value=nm;
    } else {
        const nm=base.replace(/[-_]/g,' ').trim();
        if(nm) document.getElementById('apkName').value=nm;
    }
}

// ── Maintenance toggle UI ──
function toggleMaintUI(){
    const t=document.getElementById('maintTrack');
    const th=document.getElementById('maintThumb');
    const v=document.getElementById('maintVal');
    const on=t.classList.contains('bg-red-500');
    t.classList.toggle('bg-red-500',!on);
    t.classList.toggle('bg-slate-300',on);
    th.classList.toggle('left-[22px]',!on);
    th.classList.toggle('left-0.5',on);
    v.value=on?'0':'1';
}

// ── Init lucide ──
document.addEventListener('DOMContentLoaded',()=>{ if(window.lucide) lucide.createIcons(); });
</script>
</body>
</html>
