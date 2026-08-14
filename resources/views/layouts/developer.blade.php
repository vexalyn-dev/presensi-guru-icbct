<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dev Panel · ICB CT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-outfit { font-family: 'Outfit', sans-serif; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Premium Backgrounds */
        .bg-mesh-light {
            background-color: #f8fafc;
            background-image: radial-gradient(at 40% 20%, hsla(270,100%,96%,1) 0px, transparent 50%),
                              radial-gradient(at 80% 0%, hsla(240,100%,96%,1) 0px, transparent 50%),
                              radial-gradient(at 0% 50%, hsla(280,100%,96%,1) 0px, transparent 50%);
        }
        .dark .bg-mesh-dark {
            background-color: #030712;
            background-image: radial-gradient(at 10% 20%, rgba(88, 28, 135, 0.15) 0px, transparent 50%),
                              radial-gradient(at 90% 10%, rgba(67, 56, 202, 0.15) 0px, transparent 50%),
                              radial-gradient(at 50% 80%, rgba(124, 58, 237, 0.1) 0px, transparent 50%);
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.05);
        }
        .dark .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.2);
        }

        /* Nav Item Animations */
        .nav-item {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
        }
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
            z-index: -1;
        }
        .nav-item:hover::before { left: 100%; }
        .nav-item::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0; width: 3px;
            background: linear-gradient(to bottom, #8b5cf6, #3b82f6);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        .nav-item.active-dev::after { transform: scaleY(1); }

        /* General Animations */
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        
        /* Floating Animation */
        .float-anim { animation: floating 6s ease-in-out infinite; }
        @keyframes floating { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }

        /* Loader */
        .loader-ring {
            width: 80px; height: 80px; border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: #8b5cf6; border-right-color: #3b82f6;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="h-full bg-mesh-light dark:bg-mesh-dark text-slate-800 dark:text-slate-200 transition-colors duration-500 selection:bg-purple-500/30"
      x-data="{
          sidebarOpen: false,
          darkMode: (() => { try { return localStorage.getItem('devTheme') === 'dark'; } catch(e){ return false; } })(),
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
<div id="devLoader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 dark:bg-[#030712] transition-opacity duration-700">
    <div class="relative flex items-center justify-center mb-8">
        <div class="loader-ring absolute"></div>
        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 animate-pulse">
            <i data-lucide="code-2" class="w-6 h-6 text-white"></i>
        </div>
    </div>
    <h2 class="text-xl font-outfit font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-blue-600 mb-2">Vexalyn Dev</h2>
    <p class="text-sm text-slate-500 tracking-widest uppercase mb-6" id="devLoaderMsg">Initializing Workspace...</p>
    <div class="w-48 h-1 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
        <div id="devLoaderBar" class="h-full bg-gradient-to-r from-purple-600 to-blue-600 rounded-full" style="width:0;transition:width 0.1s ease"></div>
    </div>
</div>

{{-- ══ WELCOME MODAL ══ --}}
@isset($latestUpdate)
<div id="devModal" class="fixed inset-0 z-[8888] bg-slate-900/40 dark:bg-black/60 backdrop-blur-md hidden items-center justify-center p-4">
    <div id="devModalBox" class="glass-card rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl relative"
         style="transform:translateY(40px) scale(0.95); opacity:0; transition:all 0.5s cubic-bezier(0.16,1,0.3,1)">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-blue-600/5 z-0"></div>
        <div class="relative z-10 p-8 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-purple-500/30 transform rotate-3">
                <i data-lucide="rocket" class="w-8 h-8 text-white -rotate-3"></i>
            </div>
            <h2 class="text-2xl font-outfit font-bold text-slate-800 dark:text-white mb-2">Workspace Ready</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">ICB CT Developer Environment loaded successfully.</p>
            
            @if($latestUpdate)
            <div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50 rounded-2xl p-4 mb-6 text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">{{ $latestUpdate->type }}</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">v{{ $latestUpdate->version }}</span>
                    </div>
                    <span class="text-[10px] text-slate-400">{{ $latestUpdate->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-1">{{ $latestUpdate->title }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">{{ $latestUpdate->content }}</p>
            </div>
            @endif
            
            <button onclick="closeDevModal()"
                class="w-full py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                Enter Dashboard
            </button>
        </div>
    </div>
</div>
@endisset

{{-- Mobile Overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-slate-900/40 dark:bg-black/40 z-40 lg:hidden backdrop-blur-sm"></div>

{{-- ══ SIDEBAR ══ --}}
<aside id="sidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col glass-card border-r-0 lg:border-r border-slate-200/50 dark:border-slate-800/50 transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1)">
    
    {{-- Logo Area --}}
    <div class="h-24 flex items-center px-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex items-center gap-4">
            <div class="relative flex-shrink-0">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl blur opacity-50"></div>
                <div class="relative w-12 h-12 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="code-2" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="font-outfit font-bold text-lg text-slate-800 dark:text-white leading-none mb-1">Dev Panel</h1>
                <p class="text-[10px] font-semibold text-purple-600 dark:text-purple-400 tracking-widest uppercase">Workspace</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-8 space-y-8 overflow-y-auto no-scrollbar">
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase mb-4">Main Menu</p>
            <div class="space-y-1.5">
                <button onclick="devShowSection('sec-dashboard')" id="devnav-sec-dashboard"
                    class="nav-item active-dev w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-semibold rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i><span>Dashboard</span>
                </button>
                <button onclick="devShowSection('sec-apk')" id="devnav-sec-apk"
                    class="nav-item w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200">
                    <i data-lucide="smartphone" class="w-5 h-5"></i><span>APK Manager</span>
                </button>
                <button onclick="devShowSection('sec-maint')" id="devnav-sec-maint"
                    class="nav-item w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i><span>System State</span>
                </button>
                <button onclick="devShowSection('sec-updates')" id="devnav-sec-updates"
                    class="nav-item w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200">
                    <i data-lucide="rocket" class="w-5 h-5"></i><span>Releases</span>
                </button>
            </div>
        </div>
        
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase mb-4">Quick Links</p>
            <div class="space-y-1.5">
                <a href="{{ isset($secret) ? route('developer.clear-cache', $secret) : '#' }}" onclick="return confirm('Clear application cache?')"
                   class="nav-item flex items-center gap-3.5 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-teal-600 dark:hover:text-teal-400 transition-colors group">
                    <i data-lucide="zap" class="w-4.5 h-4.5 group-hover:fill-teal-500/20"></i><span>Flush Cache</span>
                </a>
                <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank"
                   class="nav-item flex items-center gap-3.5 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
                    <i data-lucide="database" class="w-4.5 h-4.5 group-hover:fill-blue-500/20"></i><span>Run Migrations</span>
                </a>
                <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank"
                   class="nav-item flex items-center gap-3.5 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors group">
                   <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                    <span>Repository</span>
                </a>
            </div>
        </div>
    </nav>
    
    {{-- User Profile Area --}}
    <div class="p-4 mt-auto border-t border-slate-200/50 dark:border-slate-800/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-100/50 dark:bg-slate-800/50">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center text-white font-outfit font-bold shadow-md">
                VA
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 dark:text-white truncate">Vio Atmajaya</p>
                <p class="text-[10px] text-slate-500 truncate">Lead Developer</p>
            </div>
            <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}" title="Exit Panel"
               class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400 transition-colors text-slate-400">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</aside>

{{-- ══ MAIN CONTENT AREA ══ --}}
<div class="lg:ml-72 min-h-screen flex flex-col relative z-10">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 h-20 px-6 sm:px-10 flex items-center justify-between glass-card border-x-0 border-t-0 rounded-none border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex items-center gap-5">
            <button @click="sidebarOpen = true"
                    class="lg:hidden flex h-10 w-10 items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm transition-all hover:bg-slate-50">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div>
                <h2 class="text-lg font-outfit font-bold text-slate-800 dark:text-white" id="devPageTitle">@yield('page-title', 'Dashboard')</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium" id="devRealtimeClock">Loading time...</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Environment Badge --}}
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20">
                <i data-lucide="activity" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 tracking-wider">PRODUCTION</span>
            </div>

            {{-- Theme Toggle --}}
            <button @click="darkMode = !darkMode"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm transition-all hover:scale-105 active:scale-95">
                <i data-lucide="sun"  x-show="!darkMode" class="w-5 h-5"></i>
                <i data-lucide="moon" x-show="darkMode"  x-cloak class="w-5 h-5"></i>
            </button>
        </div>
    </header>

    {{-- Session Alerts --}}
    <main class="flex-1 p-6 sm:p-10 max-w-7xl mx-auto w-full">
        @if(session('success') || session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="mb-8">
            @if(session('success'))
            <div class="flex items-center gap-4 p-4 bg-emerald-50/80 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-2xl backdrop-blur-sm slide-up shadow-sm">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-4 p-4 bg-red-50/80 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-2xl backdrop-blur-sm slide-up shadow-sm">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-octagon" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    function initIcons() {
        if (!window.lucide || typeof lucide.createIcons !== 'function') return;
        try { lucide.createIcons(); } catch(e) {}
    }

    function updateDevClock() {
        const now = new Date();
        const el = document.getElementById('devRealtimeClock');
        if (el) {
            const timeStr = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute:'2-digit', second:'2-digit' });
            const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            el.textContent = `${dateStr} • ${timeStr}`;
        }
    }

    // Modern Loading Screen Logic
    let devLP = 0;
    const devLB = document.getElementById('devLoaderBar');
    const devMsg = document.getElementById('devLoaderMsg');
    const msgs = ['Authenticating...', 'Fetching metrics...', 'Rendering UI...', 'Ready'];
    
    const devLT = setInterval(() => {
        devLP += Math.random() * 15 + 5;
        if (devLP > 100) devLP = 100;
        if(devLB) devLB.style.width = devLP + '%';
        if(devMsg) devMsg.textContent = msgs[Math.floor((devLP/100) * (msgs.length-1))];
        
        if (devLP >= 100) { 
            clearInterval(devLT); 
            setTimeout(() => {
                const ld = document.getElementById('devLoader');
                if(ld) {
                    ld.style.opacity = '0';
                    setTimeout(() => {
                        ld.style.display = 'none';
                        initIcons();
                        updateDevClock();
                        setInterval(updateDevClock, 1000);
                        
                        const modal = document.getElementById('devModal');
                        if (modal) {
                            const box = document.getElementById('devModalBox');
                            modal.classList.remove('hidden');
                            modal.style.display = 'flex';
                            requestAnimationFrame(() => requestAnimationFrame(() => {
                                box.style.transform = 'translateY(0) scale(1)';
                                box.style.opacity = '1';
                            }));
                        }
                    }, 700);
                }
            }, 400); 
        }
    }, 120);

    function closeDevModal() {
        const box = document.getElementById('devModalBox');
        if(box) {
            box.style.transform = 'translateY(30px) scale(0.95)';
            box.style.opacity = '0';
            setTimeout(() => {
                const m = document.getElementById('devModal');
                if(m) { m.style.display = 'none'; m.classList.add('hidden'); }
            }, 500);
        }
    }

    // Navigation Logic
    const devSections = ['sec-dashboard','sec-apk','sec-maint','sec-updates'];
    const devTitles = {'sec-dashboard':'Dashboard Overview','sec-apk':'Application Packages','sec-maint':'System State','sec-updates':'Release Management'};
    
    function devShowSection(id) {
        devSections.forEach(s => {
            const el = document.getElementById(s);
            if (el) {
                if(s === id) {
                    el.classList.remove('hidden');
                    // Retrigger animation
                    el.style.animation = 'none';
                    el.offsetHeight; 
                    el.style.animation = null;
                    el.classList.add('fade-in-up');
                } else {
                    el.classList.add('hidden');
                    el.classList.remove('fade-in-up');
                }
            }
            
            const nav = document.getElementById('devnav-' + s);
            if (nav) {
                if (s === id) {
                    nav.className = "nav-item active-dev w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-semibold rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-300 shadow-sm";
                } else {
                    nav.className = "nav-item w-full flex items-center gap-3.5 px-4 py-3.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200";
                }
            }
        });
        const pt = document.getElementById('devPageTitle');
        if (pt) pt.textContent = devTitles[id] || 'Dashboard';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initIcons();
        devShowSection('sec-dashboard');
    });
</script>
@yield('scripts')
</body>
</html>
