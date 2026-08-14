<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dev Panel · ICB CT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7F9FC;
            color: #1E293B;
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Card & Shadow Styles */
        .hc-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        /* Sidebar Nav Item */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 24px;
            color: #64748B;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
        }
        .nav-item:hover {
            color: #6366f1;
            background-color: #f8fafc;
        }
        .nav-item.active-dev {
            color: #6366f1;
        }
        .nav-item.active-dev::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            bottom: 10%;
            width: 4px;
            background-color: #6366f1;
            border-radius: 0 4px 4px 0;
        }

        /* Primary Button */
        .btn-primary {
            background-color: #6366f1;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #4f46e5;
            transform: translateY(-1px);
        }

        /* Utilities */
        .text-purple-brand { color: #6366f1; }
        .bg-purple-brand { background-color: #6366f1; }
        .bg-purple-light { background-color: #EEF2FF; }

        [x-cloak] { display: none !important; }
        
        /* Fade animation */
        .fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="h-screen flex overflow-hidden text-slate-800">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm"></div>

    {{-- ══ SIDEBAR ══ --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out flex flex-col">
        
        {{-- Logo --}}
        <div class="h-[80px] flex items-center px-6 gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                <i data-lucide="code-2" class="w-5 h-5"></i>
            </div>
            <span class="font-bold text-xl tracking-tight text-slate-800">Dev-panel</span>
        </div>

        {{-- Primary Action --}}
        <div class="px-6 mb-6 mt-2">
            <button class="w-full btn-primary py-3 flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/30">
                <span>New Release</span>
                <i data-lucide="plus" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto no-scrollbar py-2">
            <a onclick="devShowSection('sec-dashboard')" id="devnav-sec-dashboard" class="nav-item active-dev">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Overview</span>
            </a>
            <a onclick="devShowSection('sec-apk')" id="devnav-sec-apk" class="nav-item">
                <i data-lucide="smartphone" class="w-5 h-5"></i>
                <span>APK Manager</span>
            </a>
            <a onclick="devShowSection('sec-maint')" id="devnav-sec-maint" class="nav-item">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
                <span>System State</span>
            </a>
            <a onclick="devShowSection('sec-updates')" id="devnav-sec-updates" class="nav-item">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Releases</span>
            </a>
            <a href="{{ url('/dashboard') }}" class="nav-item">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span>Main App</span>
            </a>
        </nav>

        {{-- Bottom Promo Card --}}
        <div class="p-6 mt-auto">
            <div class="bg-indigo-50 rounded-2xl p-5 text-center relative overflow-hidden group">
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-indigo-500 rounded-full opacity-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-10 h-10 bg-indigo-500 rounded-full text-white flex items-center justify-center mx-auto mb-3 shadow-md">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mb-1">GitHub Repo</h4>
                <p class="text-[11px] text-slate-500 mb-4">View source code & commits</p>
                <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" class="block w-full py-2 bg-white rounded-lg text-indigo-600 font-bold text-xs shadow-sm hover:shadow-md transition-shadow">
                    Open Repository
                </a>
            </div>
        </div>
    </aside>

    {{-- ══ MAIN CONTENT ══ --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden lg:ml-[260px]">
        
        {{-- Topbar --}}
        <header class="h-[80px] bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8 z-10 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-800">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                
                {{-- Search Bar --}}
                <div class="hidden sm:flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-full px-4 py-2 w-64 lg:w-96 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-300 transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" placeholder="Search..." class="bg-transparent border-none outline-none text-sm w-full text-slate-700 placeholder:text-slate-400 focus:ring-0 p-0">
                </div>
            </div>

            <div class="flex items-center gap-6">
                {{-- Notifications --}}
                <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute 0 top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                {{-- User Profile --}}
                <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
                    <div class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=6366f1&color=fff" alt="User" class="w-full h-full object-cover">
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-slate-700 leading-none">Vio Atmajaya <i data-lucide="chevron-down" class="w-3 h-3 inline-block ml-1 text-slate-400"></i></p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Scrollable Area --}}
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-[#F7F9FC]">
            @yield('content')
        </main>
    </div>

    <script>
        function initIcons() {
            if (window.lucide) lucide.createIcons();
        }
        
        // Navigation Logic
        const devSections = ['sec-dashboard','sec-apk','sec-maint','sec-updates'];
        function devShowSection(id) {
            devSections.forEach(s => {
                const el = document.getElementById(s);
                if (el) {
                    if (s === id) {
                        el.classList.remove('hidden');
                        el.classList.add('fade-in');
                    } else {
                        el.classList.add('hidden');
                        el.classList.remove('fade-in');
                    }
                }
                const nav = document.getElementById('devnav-' + s);
                if (nav) {
                    if (s === id) {
                        nav.classList.add('active-dev');
                    } else {
                        nav.classList.remove('active-dev');
                    }
                }
            });
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
