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
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .fade-in  { animation: fadeIn .4s ease-out; }
        .slide-up { animation: slideUp .4s ease-out; }
        @keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        /* Nav item — same as app.blade.php */
        .nav-item { position: relative; overflow: hidden; transition: background .22s ease, color .22s ease, transform .18s cubic-bezier(.34,1.56,.64,1); }
        .nav-item::before { content:''; position:absolute; top:0; left:-120%; width:60%; height:100%; background:linear-gradient(105deg,transparent,rgba(255,255,255,.07),transparent); transition:left .55s cubic-bezier(.4,0,.2,1); pointer-events:none; z-index:0; }
        .nav-item:hover::before { left:160%; }
        .nav-item::after { content:''; position:absolute; left:0; top:20%; bottom:20%; width:3px; border-radius:0 3px 3px 0; background:rgba(167,139,250,.7); transform:scaleY(0); transform-origin:center; transition:transform .2s cubic-bezier(.34,1.56,.64,1); }
        .nav-item:hover::after { transform:scaleY(1); }
        .nav-item.active-dev::after { display:none; }
        .card { background:white; border:1px solid rgb(226 232 240); border-radius:.75rem; }
        .dark .card { background:rgb(30 41 59); border-color:rgb(51 65 85); }
        /* Loading */
        @keyframes dev-spin { to { transform: rotate(360deg); } }
        /* Dev orb */
        .dev-orb { position:absolute; border-radius:50%; filter:blur(55px); pointer-events:none; animation:devOrb 7s ease-in-out infinite; }
        @keyframes devOrb { 0%,100%{opacity:.35} 50%{opacity:.65} }
        /* Slider */
        .dslide { position:absolute; inset:0; opacity:0; transform:translateX(28px); transition:opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1); }
        .dslide.active { opacity:1; transform:translateX(0); }
        .dslide.exit   { opacity:0; transform:translateX(-20px); transition:opacity .4s ease-in, transform .4s ease-in; }
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
<div id="devLoader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center gap-5 bg-slate-50 dark:bg-slate-950">
    <div class="relative w-16 h-16">
        <div style="position:absolute;inset:0;border-radius:50%;border:3px solid #e9d5ff;border-top-color:#7c3aed;animation:dev-spin .9s linear infinite"></div>
        <div style="position:absolute;top:9px;left:9px;right:9px;bottom:9px;border-radius:50%;border:3px solid transparent;border-bottom-color:#a855f7;animation:dev-spin .7s linear infinite reverse"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <i data-lucide="code-2" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
        </div>
    </div>
    <div class="text-center">
        <p class="text-sm font-bold text-slate-800 dark:text-white" id="devLoaderMsg">Memuat Dev Panel…</p>
        <p class="text-xs text-slate-400 mt-1">ICB CT · Vexalyn Dev</p>
    </div>
    <div class="w-48 h-1.5 bg-purple-100 dark:bg-purple-900/30 rounded-full overflow-hidden">
        <div id="devLoaderBar" class="h-full bg-gradient-to-r from-purple-700 to-purple-400 rounded-full" style="width:0;transition:width .08s linear"></div>
    </div>
</div>

{{-- ══ WELCOME MODAL ══ --}}
@isset($latestUpdate)
<div id="devModal" class="fixed inset-0 z-[8888] bg-black/55 backdrop-blur-sm hidden items-center justify-center p-4">
    <div id="devModalBox" class="bg-white dark:bg-slate-800 rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700"
         style="transform:translateY(30px) scale(.95);opacity:0;transition:all .45s cubic-bezier(.16,1,.3,1)">
        <div class="h-1.5 bg-gradient-to-r from-purple-900 via-purple-500 to-violet-400"></div>
        <div class="p-8">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-800 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-purple-400/30">
                <i data-lucide="code-2" class="w-7 h-7 text-white"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white text-center mb-2">Selamat Datang, Developer! 👋</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center leading-relaxed mb-5">
                Kamu mengakses <strong class="text-purple-700 dark:text-purple-400">Developer Dashboard</strong> ICB CT.
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
            <button onclick="closeDevModal()"
                class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-purple-800 to-purple-600 hover:from-purple-900 hover:to-purple-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-purple-400/25 hover:-translate-y-0.5 active:scale-95">
                <i data-lucide="check" class="w-4 h-4"></i> Masuk ke Dashboard
            </button>
        </div>
    </div>
</div>
@endisset

{{-- Mobile Overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"></div>

{{-- ══ SIDEBAR — identik app.blade.php, warna purple ══ --}}
<aside id="sidebar"
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
            <button onclick="devShowSection('sec-dashboard')" id="devnav-sec-dashboard"
                class="nav-item active-dev w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 bg-purple-700 text-white shadow-lg shadow-purple-700/30">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i><span>Dashboard</span>
            </button>
            <button onclick="devShowSection('sec-apk')" id="devnav-sec-apk"
                class="nav-item w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="smartphone" class="w-4 h-4"></i><span>APK Management</span>
            </button>
            <button onclick="devShowSection('sec-maint')" id="devnav-sec-maint"
                class="nav-item w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="construction" class="w-4 h-4"></i><span>Maintenance Mode</span>
            </button>
            <button onclick="devShowSection('sec-updates')" id="devnav-sec-updates"
                class="nav-item w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i data-lucide="rocket" class="w-4 h-4"></i><span>Rilis Update</span>
            </button>
        </div>
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Tools</p>
            <a href="{{ isset($secret) ? route('developer.clear-cache', $secret) : '#' }}" onclick="return confirm('Clear semua cache?')"
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

    {{-- Topbar — identik app.blade.php tapi tanpa notif, profile dev only --}}
    <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-5 h-16 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = true"
                    class="lg:hidden flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-300 flex-shrink-0">
                <i data-lucide="menu" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white" id="devPageTitle">@yield('page-title', 'Dashboard')</h2>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" id="devRealtimeClock">Memuat...</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Dark Mode Toggle — identik app.blade.php --}}
            <button @click="darkMode = !darkMode; spinAnim = true; setTimeout(() => spinAnim = false, 500)"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 cursor-pointer focus:outline-none">
                <i data-lucide="sun"  x-show="!darkMode" class="w-5 h-5 text-slate-600 transition-transform" :class="spinAnim ? 'rotate-12' : ''"></i>
                <i data-lucide="moon" x-show="darkMode"  x-cloak class="w-5 h-5 text-purple-400 transition-transform" :class="spinAnim ? '-rotate-12' : ''"></i>
            </button>

            {{-- Profile Dropdown — dev only --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click.stop="open = !open"
                        class="flex items-center gap-2.5 p-1.5 pr-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-all hover:scale-105">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-700 to-purple-500 rounded-full flex items-center justify-center shadow-sm">
                        <i data-lucide="user" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-[11px] font-semibold text-slate-800 dark:text-white leading-tight">Vio Atmajaya</p>
                        <p class="text-[9px] text-purple-600 dark:text-purple-400 font-medium">Developer</p>
                    </div>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                </button>

                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                    {{-- Header --}}
                    <div class="p-4 bg-gradient-to-br from-purple-800 to-purple-900">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">Vio Atmajaya Saputra</p>
                                <p class="text-xs text-purple-200 truncate">vexalyndev.my.id</p>
                            </div>
                        </div>
                    </div>
                    {{-- Menu items --}}
                    <div class="py-2">
                        <a href="https://vexalyndev.my.id" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all hover:pl-5">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            <span>Profil Developer</span>
                        </a>
                        <hr class="my-2 border-slate-200 dark:border-slate-700">
                        <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all hover:pl-5">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Secret badge --}}
            <span class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 text-purple-700 dark:text-purple-400 text-[11px] font-bold">
                <i data-lucide="shield-check" class="w-3 h-3"></i> SECRET
            </span>
        </div>
    </header>

    {{-- Session alerts — identik app.blade.php --}}
    <main class="flex-1 p-5 lg:p-6 overflow-x-hidden">
        @if(session('success') || session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
            @if(session('success'))
            <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl slide-up">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0"></i>
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl slide-up">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0"></i>
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- ══ SCRIPTS — identik app.blade.php ══ --}}
<script>
    function initIcons() {
        if (!window.lucide || typeof lucide.createIcons !== 'function') return;
        try {
            const opts = {};
            if (lucide.icons && typeof lucide.icons === 'object') opts.icons = lucide.icons;
            lucide.createIcons(opts);
        } catch(e) { console.warn('Lucide:', e); }
    }

    function updateDevClock() {
        const now = new Date();
        const el = document.getElementById('devRealtimeClock');
        if (el) el.textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
    }

    // Loading screen
    const devLMsgs = ['Memuat Dev Panel…','Menyiapkan data…','Hampir selesai…','Siap! ✓'];
    let devLP = 0;
    const devLB = document.getElementById('devLoaderBar');
    const devLT = setInterval(() => {
        devLP += Math.random() * 22 + 8;
        if (devLP > 100) devLP = 100;
        devLB.style.width = devLP + '%';
        document.getElementById('devLoaderMsg').textContent = devLMsgs[Math.floor((devLP/100) * (devLMsgs.length-1))];
        if (devLP >= 100) { clearInterval(devLT); setTimeout(devShowApp, 350); }
    }, 110);

    function devShowApp() {
        const ld = document.getElementById('devLoader');
        ld.style.transition = 'opacity .5s ease';
        ld.style.opacity = '0';
        setTimeout(() => {
            ld.style.display = 'none';
            initIcons();
            updateDevClock();
            setInterval(updateDevClock, 60000);
            // Show welcome modal
            const modal = document.getElementById('devModal');
            if (modal) {
                const box = document.getElementById('devModalBox');
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    box.style.transform = 'translateY(0) scale(1)';
                    box.style.opacity = '1';
                    initIcons();
                }));
            }
        }, 500);
    }

    function closeDevModal() {
        const box = document.getElementById('devModalBox');
        box.style.transform = 'translateY(20px) scale(.95)';
        box.style.opacity = '0';
        setTimeout(() => {
            const m = document.getElementById('devModal');
            m.style.display = 'none';
            m.classList.add('hidden');
        }, 350);
    }
    const _dm = document.getElementById('devModal');
    if (_dm) _dm.addEventListener('click', e => { if(e.target === _dm) closeDevModal(); });

    // Section navigation
    const devSections = ['sec-dashboard','sec-apk','sec-maint','sec-updates'];
    const devTitles = {'sec-dashboard':'Dashboard','sec-apk':'APK Management','sec-maint':'Maintenance Mode','sec-updates':'Rilis Update'};
    function devShowSection(id) {
        devSections.forEach(s => {
            const el = document.getElementById(s);
            if (el) el.classList.toggle('hidden', s !== id);
            const nav = document.getElementById('devnav-' + s);
            if (nav) {
                if (s === id) {
                    nav.className = nav.className.replace(/text-slate-600|hover:bg-slate-100|dark:text-slate-400|dark:hover:bg-slate-800/g,'').trim();
                    nav.classList.add('active-dev','bg-purple-700','text-white','shadow-lg','shadow-purple-700/30');
                } else {
                    nav.classList.remove('active-dev','bg-purple-700','text-white','shadow-lg','shadow-purple-700/30');
                    nav.classList.add('text-slate-600','hover:bg-slate-100','dark:text-slate-400','dark:hover:bg-slate-800');
                }
            }
        });
        const pt = document.getElementById('devPageTitle');
        if (pt) pt.textContent = devTitles[id] || 'Dashboard';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // APK auto-fill
    function handleApk(file) {
        if (!file) return;
        document.getElementById('apkZoneTxt').textContent = file.name;
        document.getElementById('apkZone').classList.add('border-purple-500');
        const base = file.name.replace(/\.apk$/i, '');
        const vm = base.match(/[vV]?(\d+\.\d+(?:\.\d+)?(?:\.\d+)?)/);
        if (vm) {
            document.getElementById('apkVer').value = vm[1];
            const nm = base.replace(/[-_\s]*[vV]?\d+\.\d+(?:\.\d+)?(?:\.\d+)?[-_\s]*/g,'').replace(/[-_]/g,' ').trim();
            if (nm) document.getElementById('apkName').value = nm;
        } else {
            const nm = base.replace(/[-_]/g,' ').trim();
            if (nm) document.getElementById('apkName').value = nm;
        }
    }

    // Maintenance toggle
    function toggleMaintUI() {
        const t = document.getElementById('maintTrack');
        const th = document.getElementById('maintThumb');
        const v = document.getElementById('maintVal');
        const on = t.classList.contains('bg-red-500');
        t.classList.toggle('bg-red-500', !on);
        t.classList.toggle('bg-slate-300', on);
        t.classList.toggle('dark:bg-slate-600', on);
        th.classList.toggle('left-[22px]', !on);
        th.classList.toggle('left-0.5', on);
        v.value = on ? '0' : '1';
    }

    document.addEventListener('DOMContentLoaded', () => {
        initIcons();
        devShowSection('sec-dashboard');
    });
</script>
@yield('scripts')
</body>
</html>
