<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .teacher-nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #64748B; text-decoration: none; border-radius: 12px; transition: all 0.2s; }
        .teacher-nav-item:hover { background: #F1F5F9; color: #0F172A; }
        .teacher-nav-item.active { background: #0F172A; color: white; }
        .teacher-card,
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn-primary { background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(15,23,42,0.3); }
        .fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false" class="min-h-screen">
        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
             x-cloak></div>

        {{-- Sidebar: off-canvas mobile, fixed desktop --}}
        <aside id="teacher-sidebar"
               @click.outside="sidebarOpen = false"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed top-0 left-0 z-50 flex h-screen w-64 max-w-[85vw] flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300 ease-out lg:shadow-none">
            <div class="flex flex-1 flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto overscroll-contain p-5">
                    <div class="flex items-center justify-between gap-2 lg:hidden mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-6 h-6 text-navy-900"></i>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-sm font-bold text-navy-800 truncate">ICB CINTA TEKNIKA</h1>
                                <p class="text-[9px] text-slate-500">GURU</p>
                            </div>
                        </div>
                        <button type="button" @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 shrink-0" aria-label="Tutup menu">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="hidden lg:flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="graduation-cap" class="w-6 h-6 text-navy-900"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-lg font-bold text-navy-800 leading-tight">ICB CINTA TEKNIKA</h1>
                            <p class="text-[10px] text-slate-500">ABSENSI GURU</p>
                        </div>
                    </div>

                    <nav class="space-y-1">
                        <a href="{{ route('teacher.dashboard') }}" @click="sidebarOpen = false" class="teacher-nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('teacher.schedule') }}" @click="sidebarOpen = false" class="teacher-nav-item {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">
                            <i data-lucide="calendar" class="w-5 h-5 shrink-0"></i>
                            <span>Jadwal Mengajar</span>
                        </a>
                        <a href="{{ route('teacher.attendance') }}" @click="sidebarOpen = false" class="teacher-nav-item {{ request()->routeIs('teacher.attendance') ? 'active' : '' }}">
                            <i data-lucide="scan-line" class="w-5 h-5 shrink-0"></i>
                            <span>Absensi Saya</span>
                        </a>
                        <a href="{{ route('teacher.profile') }}" @click="sidebarOpen = false" class="teacher-nav-item {{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                            <i data-lucide="user" class="w-5 h-5 shrink-0"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="{{ route('teacher.dashboard') }}#izin" @click="sidebarOpen = false" class="teacher-nav-item">
                            <i data-lucide="file-plus" class="w-5 h-5 shrink-0"></i>
                            <span>Izin / Sakit</span>
                        </a>
                        <a href="{{ route('leaves.index') }}" @click="sidebarOpen = false" class="teacher-nav-item {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                            <i data-lucide="clipboard-list" class="w-5 h-5 shrink-0"></i>
                            <span>Riwayat Izin</span>
                        </a>
                    </nav>
                </div>

                <div class="shrink-0 border-t border-slate-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <img src="{{ Auth::user()->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-slate-100 dark:border-slate-700 shadow-sm">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-navy-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-medium transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main: full width on mobile, offset on lg so nothing sits under sidebar --}}
        <div class="min-h-screen flex flex-col lg:ml-64 w-full min-w-0">
            <header class="sticky top-0 z-30 flex shrink-0 items-center justify-between gap-4 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur-md sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-700 shrink-0" aria-label="Buka menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-navy-800 truncate">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs sm:text-sm text-slate-500 truncate">{{ Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY · HH:mm') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <button type="button" class="hidden sm:block p-2 hover:bg-slate-100 rounded-lg transition-colors text-slate-600" aria-label="Notifikasi">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </button>
                    <img src="{{ Auth::user()->photo_url }}" alt="" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-slate-200 shadow-sm">
                </div>
            </header>

            <div class="flex-1 w-full min-w-0 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function initTeacherLucide() {
            if (!window.lucide || typeof lucide.createIcons !== 'function') return;
            try {
                const opts = lucide.icons && typeof lucide.icons === 'object' ? { icons: lucide.icons } : {};
                lucide.createIcons(opts);
            } catch (e) {
                console.warn('Lucide:', e);
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            initTeacherLucide();
            requestAnimationFrame(initTeacherLucide);
        });
        document.addEventListener('alpine:initialized', function() {
            initTeacherLucide();
        });
    </script>
</body>
</html>
