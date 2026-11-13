<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') — Presensi Guru ICB CT | SMK Cinta Teknika</title>

    <!-- SEO Meta -->
    <meta name="description" content="@yield('meta-description', 'Portal Guru Presensi Digital ICB CT — SMK Cinta Teknika. Kelola absensi harian & kelas via QR Code, jadwal mengajar, izin/sakit, dan riwayat kehadiran.')">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="{{ url()->current() }}">

    @php
        $ogTitle  = trim(\Illuminate\Support\Facades\View::yieldContent('og-title'))
                 ?: trim(\Illuminate\Support\Facades\View::yieldContent('page-title'))
                 ?: 'Portal Guru';
        $ogTitle .= ' — Presensi Guru ICB CT | SMK Cinta Teknika';
        $ogDesc   = trim(\Illuminate\Support\Facades\View::yieldContent('og-description'))
                 ?: 'Portal Guru Presensi Digital ICB CT — SMK Cinta Teknika. Kelola absensi harian & kelas via QR Code, jadwal mengajar, izin/sakit, dan riwayat kehadiran.';
        $ogImage  = trim(\Illuminate\Support\Facades\View::yieldContent('og-image'))
                 ?: asset('images/og-banner.png');
    @endphp

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDesc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $ogTitle }}">
    <meta property="og:site_name" content="Presensi Guru ICB CT">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $ogTitle }}">

    <!-- Favicon Pack -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon-180x180.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.webmanifest') }}">
    <meta name="theme-color" content="#0f172a">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" onload="if(window.lucide)lucide.createIcons()"></script>
    <script>
        // Fallback: init setelah DOM siap (saat lucide sudah tersedia)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) lucide.createIcons();
        });
        // Re-init setelah navigasi (untuk halaman yang load setelah DOMContentLoaded)
        window.initLucide = function() { if (window.lucide) lucide.createIcons(); };
    </script>

    {{-- Define Alpine component functions BEFORE Alpine loads (deferred) --}}
    <script>
        window.notificationDropdown = function() {
            return { open: false, markRead() {}, init() {} };
        };
        window.notificationDropdownAdmin = window.notificationDropdown;
    </script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* ══ NAV ITEM — PREMIUM ANIMATIONS ══ */
        .nav-item {
            position: relative; overflow: hidden;
            transition: background 0.22s ease, color 0.22s ease,
                        transform 0.18s cubic-bezier(0.34,1.56,0.64,1),
                        box-shadow 0.22s ease !important;
        }
        .nav-item::before {
            content: ''; position: absolute;
            top: 0; left: -120%; width: 60%; height: 100%;
            background: linear-gradient(105deg,transparent 0%,rgba(255,255,255,0.07) 50%,transparent 100%);
            transition: left 0.55s cubic-bezier(0.4,0,0.2,1);
            pointer-events: none; z-index: 0;
        }
        .nav-item:hover::before { left: 160%; }
        .nav-item::after {
            content: ''; position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: rgba(250,204,21,0.7);
            transform: scaleY(0); transform-origin: center;
            transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
        }
        .nav-item:hover::after { transform: scaleY(1); }
        .nav-item.bg-navy-800::after,
        .nav-item[class*="bg-navy-800"]::after { display: none; }
        .nav-item:hover:not([class*="bg-navy-800"]) { transform: translateX(3px); }
        .nav-item i[data-lucide], .nav-item svg { transition: transform 0.2s ease; }
        .nav-item:hover i[data-lucide], .nav-item:hover svg { transform: scale(1.15); transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1); }
        .nav-item:active { transform: translateX(2px) scale(0.97) !important; transition: transform 0.08s ease !important; }
        .nav-item.bg-navy-800, .nav-item[class*="bg-navy-800"] {
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.08), 0 4px 12px rgba(15,23,42,0.25) !important;
        }

        /* ══════════════════════════════════════════
           SIDEBAR ICON ANIMATIONS
        ══════════════════════════════════════════ */
        @keyframes sidebarIconBounce {
            0%, 100% { transform: translateY(0) scale(1); }
            35% { transform: translateY(-4px) scale(1.15); }
            65% { transform: translateY(-2px) scale(1.05); }
        }
        @keyframes sidebarIconShake {
            0%, 100% { transform: rotate(0deg); }
            15% { transform: rotate(-8deg); }
            30% { transform: rotate(8deg); }
            45% { transform: rotate(-6deg); }
            60% { transform: rotate(6deg); }
            75% { transform: rotate(-3deg); }
            90% { transform: rotate(3deg); }
        }
        @keyframes sidebarIconPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.25); opacity: 0.85; }
        }
        @keyframes sidebarIconWiggle {
            0%, 100% { transform: skewX(0deg); }
            20% { transform: skewX(-12deg); }
            40% { transform: skewX(10deg); }
            60% { transform: skewX(-6deg); }
            80% { transform: skewX(4deg); }
        }
        .sidebar-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }
        .nav-item:hover .sidebar-icon {
            animation: sidebarIconBounce 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        .sidebar-icon.shake:hover { animation: sidebarIconShake 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }
        .sidebar-icon.pulse:hover { animation: sidebarIconPulse 0.6s ease-in-out both; }
        .sidebar-icon.wiggle:hover { animation: sidebarIconWiggle 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }
        .sidebar-icon.bounce:hover { animation: sidebarIconBounce 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }

        /* Reset body margin/padding */
        body { margin: 0; padding: 0; }

        /* Hide scrollbar visually but keep scroll functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;             /* Chrome, Safari, Opera */
        }
        /* Standardized profile avatar used across teacher pages */
        .profile-avatar {
            width: 64px; /* matches dashboard w-16 */
            height: 64px;
            border-radius: 9999px;
            object-fit: cover;
            border: 3px solid rgba(226, 232, 240, 0.8); /* slate-200 approx */
            box-shadow: 0 8px 20px rgba(2,6,23,0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        @media (max-width: 640px) {
            .profile-avatar { width: 48px; height: 48px; }
        }

        /* Marquee animation for dashboard reminders */
        .marquee-container {
            mask-image: linear-gradient(to right, transparent, black 4px, black calc(100% - 4px), transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 4px, black calc(100% - 4px), transparent);
        }
        .marquee-track {
            display: inline-flex;
            white-space: nowrap;
            animation: marquee-scroll 18s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }
        @keyframes marquee-scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-900 m-0 p-0 overflow-x-hidden">
    @php
        // Get teacher data for current user with error handling
        $teacherData = null;
        $teacherSubject = null;

        try {
            $teacherData = \App\Models\Teacher::where('user_id', auth()->id())->first();
            $teacherSubject = $teacherData ? $teacherData->major_specialty : null;
        } catch (\Exception $e) {
            // Model belum ada atau error lain
            $teacherSubject = null;
        }
    @endphp
    <div class="flex min-h-screen m-0 p-0 overflow-x-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar -->
        <aside id="sidebar" 
               class="fixed inset-y-0 left-0 z-50 flex h-full min-h-screen w-64 flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Logo -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                    @if($appSettings->app_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($appSettings->app_logo))
                        <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="school" class="w-5 h-5 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-sm font-bold text-navy-800 dark:text-white">{{ $appSettings->app_name ?? 'ICB CINTA TEKNIKA' }}</h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Portal Guru</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.dashboard') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 sidebar-icon bounce"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('teacher.schedule') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.schedule')
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="calendar-range" class="w-4 h-4 sidebar-icon pulse"></i>
                    <span>Jadwal Mengajar</span>
                </a>

                <a href="{{ route('teacher.work-schedule') }}"
                    class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                           {{ request()->routeIs('teacher.work-schedule')
                               ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                               : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}"
                    style="display:none">
                    <i data-lucide="briefcase" class="w-4 h-4 sidebar-icon bounce"></i>
                    <span>Jadwal Kerja</span>
                </a>

                <a href="{{ route('teacher.attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan-line" class="w-4 h-4 sidebar-icon bounce"></i>
                    <span>Presensi Harian</span>
                </a>

                <a href="{{ route('teacher.class-attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.class-attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan" class="w-4 h-4 sidebar-icon wiggle"></i>
                    <span>Presensi Kelas</span>
                </a>

                <a href="{{ route('teacher.history') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.history*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="history" class="w-4 h-4 sidebar-icon shake"></i>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('teacher.leave') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.leave*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="file-text" class="w-4 h-4 sidebar-icon wiggle"></i>
                    <span>Izin/Sakit</span>
                </a>

                <a href="{{ route('teacher.support') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.support*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="life-buoy" class="w-4 h-4 sidebar-icon wiggle"></i>
                    <span>Pusat Bantuan</span>
                </a>

                <a href="{{ route('download-apk') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('download-apk')
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="smartphone" class="w-4 h-4 sidebar-icon bounce"></i>
                    <span>Download APK</span>
                </a>
            </nav>
        </aside>

           <!-- Notification config (used by external JS) -->
           <div id="laravel-config" style="display:none;"
               data-unread-url="{{ route('teacher.notifications.api.unread') }}"
               data-user-id="{{ auth()->id() ?? '' }}"></div>

           <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen min-w-0">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-20">
                <div class="px-3 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                        <!-- Hamburger Menu (Desktop/Tablet hanya — Mobile pakai bottom nav) -->
                        <button @click="sidebarOpen = true" 
                                class="hidden lg:flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-300 flex-shrink-0">
                            <i data-lucide="menu" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>

                        <!-- Logo mobile (hanya di mobile, ganti hamburger) -->
                        <div class="flex lg:hidden items-center gap-2 flex-shrink-0">
                            @if($appSettings->app_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($appSettings->app_logo))
                                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-7 h-7 object-contain">
                            @else
                                <div class="w-7 h-7 bg-gradient-to-br from-navy-800 to-navy-900 rounded-lg flex items-center justify-center">
                                    <i data-lucide="school" class="w-4 h-4 text-white"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white truncate">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} pukul {{ now()->format('H.i') }}</p>
                            @yield('topbar-reminder')
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
                        <!-- Dark/Light Mode Toggle -->
                        <button onclick="toggleDarkMode()" class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-300" title="Toggle Dark Mode">
                            <i data-lucide="sun" class="w-4 h-4 sm:w-5 sm:h-5 hidden dark:block"></i>
                            <i data-lucide="moon" class="w-4 h-4 sm:w-5 sm:h-5 block dark:hidden"></i>
                        </button>

                        <!-- Notifikasi Dropdown -->
                        <div class="relative" x-data="notificationDropdown()" 
                        @click.outside="open = false"
                        x-init="init()">
                            
                            <button @click.stop="open = !open" class="relative flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-300 dark:hover:border-slate-600">
                                <i data-lucide="bell" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                @if(auth()->user()->unreadCount() > 0)
                                <span class="notification-badge absolute top-1 right-1 sm:top-1.5 sm:right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="absolute right-0 mt-3 w-72 sm:w-80 md:w-96 origin-top-right rounded-2xl border border-slate-200/80 bg-white/95 p-0 shadow-[0_20px_45px_-12px_rgba(15,23,42,0.35)] backdrop-blur-xl overflow-hidden z-50 max-h-[420px] sm:max-h-[500px] overflow-y-auto scrollbar-hide dark:border-slate-700 dark:bg-slate-800/95"
                                 x-cloak>
                                
                                <!-- Header -->
                                <div class="sticky top-0 z-10 border-b border-slate-200/80 bg-gradient-to-r from-slate-50 to-white p-3 sm:p-4 dark:border-slate-700 dark:from-slate-700/50 dark:to-slate-800/70">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">Notifikasi</h3>
                                        @php $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count(); @endphp
                                        @if($unreadCount > 0)
                                        <button onclick="markAllNotifRead('teacher')"
                                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 text-[11px] font-semibold hover:opacity-90 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            Tandai Dibaca
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse(auth()->user()->notifications()->take(5)->get() as $notif)
                                    <div class="mx-2 my-1.5 rounded-xl border border-transparent transition-all duration-200 hover:border-slate-200 hover:bg-slate-50 hover:shadow-sm dark:hover:border-slate-700 dark:hover:bg-slate-700/60 notif-item">
                                        <a href="{{ $notif->action_url ?? '#' }}" class="flex items-start p-3 min-w-0">
                                            <div class="flex items-start gap-2.5">
                                                <div class="w-8 h-8 rounded-lg {{ $notif->color }} flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="{{ $notif->icon }}" class="w-4 h-4"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs text-navy-800 dark:text-white line-clamp-1 notif-text {{ $notif->is_read ? 'font-medium opacity-60' : 'font-semibold' }}">{{ $notif->title }}</p>
                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->message }}</p>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">{{ $notif->time_ago }}</p>
                                                </div>
                                            </div>
                                        </a>
                                        {{-- Check indicator --}}
                                        <div class="shrink-0 self-center mr-3 notif-check-wrap">
                                            @if(!$notif->is_read)
                                            {{-- Belum dibaca: single check abu --}}
                                            <svg class="notif-check-single" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                            <svg class="notif-check-double hidden" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            @else
                                            {{-- Sudah dibaca: double check hijau --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="p-8 text-center">
                                        <i data-lucide="bell-off" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi</p>
                                    </div>
                                    @endforelse
                                </div>
                                
                                <!-- Footer -->
                                <div class="p-2.5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-center sticky bottom-0">
                                    <a href="{{ route('teacher.notifications') }}" class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click.stop="open = !open"
                                    class="flex items-center gap-1.5 sm:gap-3 rounded-lg sm:rounded-xl border border-slate-200/80 bg-white/80 p-1 sm:p-1.5 pr-1.5 sm:pr-2 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80 dark:hover:border-slate-600">
                                <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff' }}" 
                                     class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600 flex-shrink-0"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0F172A&color=fff'">
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white truncate max-w-[180px]">{{ auth()->user()->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Guru</span>
                                        @if($teacherSubject)
                                        <span class="px-1.5 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[9px] font-bold">
                                            {{ Str::limit($teacherSubject, 10) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 hidden sm:block transition-transform duration-200"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Backdrop untuk tutup dropdown (klik di luar) -->
                            <div x-show="open"
                                 @click.stop="open = false"
                                 class="fixed inset-0 z-[195]"
                                 style="display:none;"></div>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 @click.stop
                                 class="absolute right-0 mt-3 w-64 origin-top-right rounded-2xl border border-slate-200/60 bg-white shadow-[0_24px_48px_-8px_rgba(15,23,42,0.28),0_0_0_1px_rgba(15,23,42,0.04)] overflow-hidden z-[200] dark:border-slate-700/80 dark:bg-slate-800"
                                 style="display:none;">
                                
                                <div class="border-b border-slate-100 dark:border-slate-700/80 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-slate-700 dark:to-slate-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff' }}"
                                             class="w-10 h-10 rounded-full object-cover border-2 border-white/30 flex-shrink-0"
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0F172A&color=fff'">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-white break-words leading-snug">{{ auth()->user()->name }}</p>
                                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="text-[10px] text-white/70">Guru</span>
                                                @if($teacherSubject)
                                                <span class="px-1.5 py-0.5 bg-white/20 text-white rounded text-[9px] font-bold">
                                                    {{ $teacherSubject }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-1.5">
                                    <a href="{{ route('teacher.profile') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 rounded-xl transition-all duration-150 hover:bg-navy-800 hover:text-white dark:hover:bg-gold-400/20 dark:hover:text-gold-400 group">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 group-hover:bg-white/20 dark:group-hover:bg-gold-400/20 transition-colors">
                                            <i data-lucide="user" class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-white dark:group-hover:text-gold-400"></i>
                                        </div>
                                        <span class="font-medium">Profil Saya</span>
                                    </a>

                                    <a href="{{ route('teacher.attendance') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 rounded-xl transition-all duration-150 hover:bg-navy-800 hover:text-white dark:hover:bg-gold-400/20 dark:hover:text-gold-400 group">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 group-hover:bg-white/20 dark:group-hover:bg-gold-400/20 transition-colors">
                                            <i data-lucide="scan-line" class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-white dark:group-hover:text-gold-400"></i>
                                        </div>
                                        <span class="font-medium">Presensi Harian</span>
                                    </a>
                                </div>

                                <div class="border-t border-slate-100 dark:border-slate-700/80 p-1.5">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 rounded-xl transition-all duration-150 hover:bg-red-50 dark:hover:bg-red-900/20 group">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                                                <i data-lucide="log-out" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
                                            </div>
                                            <span class="font-medium">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-3 sm:p-6 w-full overflow-x-hidden min-w-0 box-border">
                @if (session('success') || session('error') || session('warning'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2">
                    @if (session('success'))
                    <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl slide-up">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0"></i>
                        <p class="text-sm text-green-800 dark:text-green-200">{{ is_array(session('success')) ? implode(' ', session('success')) : session('success') }}</p>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl slide-up">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0"></i>
                        <p class="text-sm text-red-800 dark:text-red-200">{{ is_array(session('error')) ? implode(' ', session('error')) : session('error') }}</p>
                    </div>
                    @endif
                    @if (session('warning'))
                    <div class="mb-5 flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl slide-up">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0"></i>
                        <p class="text-sm text-amber-800 dark:text-amber-200">{{ is_array(session('warning')) ? implode(' ', session('warning')) : session('warning') }}</p>
                    </div>
                    @endif
                </div>
                @endif

                @yield('content')
                
                <!-- Spacer for Bottom Nav (Mobile Only) -->
                <div class="lg:hidden h-32"></div>
            </div>
        </main>
        
        <!-- Mobile Overlay (tidak digunakan lagi karena sidebar hidden di mobile) -->
    </div>

    {{-- ══════════════════════════════════════════
         BOTTOM NAVIGATION — Mobile Only (lg:hidden)
         5 tab: Home | Jadwal | Presensi | Riwayat | Lainnya
    ══════════════════════════════════════════ --}}
    <nav id="bottom-nav"
         class="lg:hidden fixed bottom-0 inset-x-0 z-[90] bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] safe-area-bottom"
         x-data="{
             moreOpen: false,
             _dragStartY: 0,
             _dragCurrentY: 0,
             sheetTouchStart(e) {
                 this._dragStartY = e.touches[0].clientY;
                 this._dragCurrentY = 0;
             },
             sheetTouchMove(e) {
                 const dy = e.touches[0].clientY - this._dragStartY;
                 if (dy > 0) {
                     this._dragCurrentY = dy;
                     this.$refs.moreSheet.style.transform = 'translateY(' + dy + 'px)';
                     this.$refs.moreSheet.style.transition = 'none';
                 }
             },
             sheetTouchEnd(e) {
                 this.$refs.moreSheet.style.transform = '';
                 this.$refs.moreSheet.style.transition = '';
                 if (this._dragCurrentY > 80) {
                     this.moreOpen = false;
                 }
                 this._dragCurrentY = 0;
             }
         }"
         style="padding-bottom: env(safe-area-inset-bottom, 0px);">

        @php
            $isHome       = request()->routeIs('teacher.dashboard');
            $isJadwal     = request()->routeIs('teacher.schedule');
            $isPresensi   = request()->routeIs('teacher.attendance');
            $isRiwayat    = request()->routeIs('teacher.history*');
            $isOther      = request()->routeIs('teacher.leave*') || request()->routeIs('teacher.support*') || request()->routeIs('download-apk') || request()->routeIs('teacher.profile') || request()->routeIs('teacher.notifications*') || request()->routeIs('teacher.class-attendance');
        @endphp

        <div class="flex items-stretch h-16">

            {{-- Home --}}
            <a href="{{ route('teacher.dashboard') }}" tabindex="-1"
               class="bnav-item {{ $isHome ? 'bnav-active' : '' }} flex-1 flex flex-col items-center justify-center gap-0.5 relative transition-all duration-200 active:scale-95">
                <div class="bnav-icon-wrap {{ $isHome ? 'bnav-icon-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isHome ? '2.5' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <span class="bnav-label {{ $isHome ? 'bnav-label-active' : '' }}">Home</span>
            </a>

            {{-- Jadwal --}}
            <a href="{{ route('teacher.schedule') }}" tabindex="-1"
               class="bnav-item {{ $isJadwal ? 'bnav-active' : '' }} flex-1 flex flex-col items-center justify-center gap-0.5 relative transition-all duration-200 active:scale-95">
                <div class="bnav-icon-wrap {{ $isJadwal ? 'bnav-icon-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isJadwal ? '2.5' : '2' }}">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="bnav-label {{ $isJadwal ? 'bnav-label-active' : '' }}">Jadwal</span>
            </a>

            {{-- Presensi — tombol tengah menonjol --}}
            <a href="{{ route('teacher.attendance') }}" tabindex="-1"
               class="bnav-item flex-1 flex flex-col items-center justify-center gap-0.5 relative -mt-4 transition-all duration-200 active:scale-95">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg shadow-navy-800/40 dark:shadow-gold-400/40 transition-all duration-200">
                    <svg class="w-6 h-6 text-white dark:text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0c-.693.04-1.344.436-1.736 1.039l-.821 1.316Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/>
                    </svg>
                </div>
                <span class="bnav-label {{ $isPresensi ? 'bnav-label-active text-navy-800 dark:text-gold-400' : '' }} mt-0.5">Presensi</span>
            </a>

            {{-- Riwayat --}}
            <a href="{{ route('teacher.history') }}" tabindex="-1"
               class="bnav-item {{ $isRiwayat ? 'bnav-active' : '' }} flex-1 flex flex-col items-center justify-center gap-0.5 relative transition-all duration-200 active:scale-95">
                <div class="bnav-icon-wrap {{ $isRiwayat ? 'bnav-icon-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isRiwayat ? '2.5' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <span class="bnav-label {{ $isRiwayat ? 'bnav-label-active' : '' }}">Riwayat</span>
            </a>

            {{-- Lainnya --}}
            <button type="button" tabindex="-1" @click="moreOpen = true"
                    class="bnav-item {{ $isOther ? 'bnav-active' : '' }} flex-1 flex flex-col items-center justify-center gap-0.5 relative transition-all duration-200 active:scale-95">
                <div class="bnav-icon-wrap {{ $isOther ? 'bnav-icon-active' : '' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="{{ $isOther ? '1.5' : '1' }}"/>
                        <circle cx="12" cy="12" r="{{ $isOther ? '1.5' : '1' }}"/>
                        <circle cx="19" cy="12" r="{{ $isOther ? '1.5' : '1' }}"/>
                    </svg>
                </div>
                <span class="bnav-label {{ $isOther ? 'bnav-label-active' : '' }}">Lainnya</span>
            </button>
        </div>

        {{-- ── Modal "Lainnya" — slide up dari bawah ── --}}
        <div x-show="moreOpen"
             x-cloak
             class="fixed inset-0 z-[200]"
             @click.self="moreOpen = false">

            {{-- Backdrop --}}
            <div x-show="moreOpen"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="moreOpen = false"
                 class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            {{-- Sheet --}}
            <div x-show="moreOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="absolute bottom-0 inset-x-0 bg-white dark:bg-slate-900 rounded-t-[36px] shadow-2xl overflow-hidden select-none"
                 style="padding-bottom: env(safe-area-inset-bottom, 16px);"
                 x-ref="moreSheet"
                 @touchstart.passive="sheetTouchStart($event)"
                 @touchmove.passive="sheetTouchMove($event)"
                 @touchend.passive="sheetTouchEnd($event)">

                {{-- Handle — klik atau drag ke bawah untuk tutup --}}
                <div class="flex justify-center pt-4 pb-2 cursor-grab active:cursor-grabbing"
                     @click="moreOpen = false">
                    <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="flex items-center px-5 py-2 pb-3">
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Menu Lainnya</h3>
                </div>

                {{-- Menu items --}}
                <div class="px-4 py-3 grid grid-cols-3 gap-3">

                    {{-- Presensi Kelas --}}
                    <a href="{{ route('teacher.class-attendance') }}" @click="moreOpen = false"
                       class="more-menu-item {{ request()->routeIs('teacher.class-attendance') ? 'more-menu-active' : '' }}">
                        <div class="more-menu-icon bg-violet-100 dark:bg-violet-900/30">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5z"/>
                            </svg>
                        </div>
                        <span class="more-menu-label">Presensi Kelas</span>
                    </a>

                    {{-- Izin / Sakit --}}
                    <a href="{{ route('teacher.leave') }}" @click="moreOpen = false"
                       class="more-menu-item {{ request()->routeIs('teacher.leave*') ? 'more-menu-active' : '' }}">
                        <div class="more-menu-icon bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                        </div>
                        <span class="more-menu-label">Izin / Sakit</span>
                    </a>

                    {{-- Pusat Bantuan --}}
                    <a href="{{ route('teacher.support') }}" @click="moreOpen = false"
                       class="more-menu-item {{ request()->routeIs('teacher.support*') ? 'more-menu-active' : '' }}">
                        <div class="more-menu-icon bg-green-100 dark:bg-green-900/30">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
                            </svg>
                        </div>
                        <span class="more-menu-label">Bantuan</span>
                    </a>

                    {{-- Notifikasi --}}
                    <a href="{{ route('teacher.notifications') }}" @click="moreOpen = false"
                       class="more-menu-item {{ request()->routeIs('teacher.notifications*') ? 'more-menu-active' : '' }}">
                        <div class="more-menu-icon bg-amber-100 dark:bg-amber-900/30 relative">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                            </svg>
                            @if(auth()->user()->unreadCount() > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ auth()->user()->unreadCount() > 9 ? '9+' : auth()->user()->unreadCount() }}</span>
                            @endif
                        </div>
                        <span class="more-menu-label">Notifikasi</span>
                    </a>

                    {{-- Download APK --}}
                    <a href="{{ route('download-apk') }}" @click="moreOpen = false"
                       class="more-menu-item {{ request()->routeIs('download-apk') ? 'more-menu-active' : '' }}">
                        <div class="more-menu-icon bg-slate-100 dark:bg-slate-800">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                            </svg>
                        </div>
                        <span class="more-menu-label">Download APK</span>
                    </a>

                    {{-- Logout --}}
                    <button type="button" @click="moreOpen = false"
                            onclick="document.getElementById('bnav-logout-form').submit()"
                            class="more-menu-item">
                        <div class="more-menu-icon bg-red-100 dark:bg-red-900/30">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                            </svg>
                        </div>
                        <span class="more-menu-label text-red-600 dark:text-red-400">Logout</span>
                    </button>
                </div>

                {{-- Hidden logout form --}}
                <form id="bnav-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
            </div>
        </div>
    </nav>

    <style>
        /* ── Bottom Nav Base ── */
        .bnav-item { color: #94a3b8; text-decoration: none; }
        html.dark .bnav-item { color: #64748b; }
        .bnav-active { color: #0f172a; }
        html.dark .bnav-active { color: #facc15; }

        .bnav-icon-wrap {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
        }
        .bnav-icon-active {
            transform: scale(1.12);
        }

        .bnav-label {
            font-size: 10px; font-weight: 500;
            line-height: 1; color: inherit;
        }
        .bnav-label-active { font-weight: 700; }

        /* Active indicator dot */
        .bnav-dot {
            position: absolute; bottom: 4px;
            width: 4px; height: 4px;
            background: currentColor;
            border-radius: 50%;
        }

        /* ── More Modal Menu Items ── */
        .more-menu-item {
            display: flex; flex-direction: column;
            align-items: center; gap: 6px;
            padding: 10px 4px;
            border-radius: 20px;
            transition: background 0.15s ease, transform 0.15s ease;
            cursor: pointer; text-decoration: none;
            background: transparent; border: none;
        }
        .more-menu-item:active { transform: scale(0.94); }
        .more-menu-item:hover { background: rgba(15,23,42,0.04); }
        html.dark .more-menu-item:hover { background: rgba(255,255,255,0.05); }
        .more-menu-active { background: rgba(15,23,42,0.06) !important; }
        html.dark .more-menu-active { background: rgba(250,204,21,0.1) !important; }

        .more-menu-icon {
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 16px; position: relative;
            transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
        }
        .more-menu-item:active .more-menu-icon { transform: scale(0.9); }
        .more-menu-label {
            font-size: 10px; font-weight: 600; color: #475569;
            text-align: center; line-height: 1.2;
        }
        html.dark .more-menu-label { color: #94a3b8; }

        /* Safe area for bottom nav */
        .safe-area-bottom { padding-bottom: env(safe-area-inset-bottom, 0px); }
    </style>

    @php
        $autoLogoutVal = \App\Models\Setting::get('auto_logout', 'off');
        $autoLogoutMinutes = ($autoLogoutVal !== 'off' && is_numeric($autoLogoutVal)) ? (int) $autoLogoutVal : 0;
    @endphp
    @if($autoLogoutMinutes > 0)
    <script data-timeout-minutes="{{ $autoLogoutMinutes }}" data-login-url="{{ route('login') }}">
        (function() {
            const autoLogoutScript = document.currentScript;
            const timeoutMinutes = Number(autoLogoutScript.dataset.timeoutMinutes);
            const timeoutMs = timeoutMinutes * 60 * 1000;
            let logoutTimer;
            function resetLogoutTimer() {
                clearTimeout(logoutTimer);
                logoutTimer = setTimeout(() => {
                    alert('Sesi Anda telah berakhir karena tidak ada aktivitas selama ' + timeoutMinutes + ' menit.');
                    const logoutForm = document.querySelector('form[action*="logout"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.href = autoLogoutScript.dataset.loginUrl;
                    }
                }, timeoutMs);
            }
            ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(evt => {
                window.addEventListener(evt, resetLogoutTimer, { passive: true });
            });
            resetLogoutTimer();
        })();
    </script>
    @endif

    <script src="{{ asset('js/notifications.js') }}?v={{ filemtime(public_path('js/notifications.js')) }}"></script>

    @stack('scripts')

    {{-- ── LOGOUT LOADING OVERLAY ── --}}
    <div id="logout-overlay"
         style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(10,15,30,0.82);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:20px;padding:32px 36px 28px;width:220px;text-align:center;box-shadow:0 20px 48px rgba(0,0,0,0.3);">
            <div style="position:relative;width:64px;height:64px;margin:0 auto 18px;">
                <div style="position:absolute;inset:0;border-radius:50%;border:4px solid #E2E8F0;border-top-color:#0F172A;animation:ptSpin 0.9s linear infinite;"></div>
                <div style="position:absolute;top:8px;left:8px;right:8px;bottom:8px;border-radius:50%;border:4px solid transparent;border-bottom-color:#FACC15;animation:ptSpinRev 0.7s linear infinite;"></div>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:14px;">
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:ptBounce 0.6s ease-in-out infinite;"></span>
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:ptBounce 0.6s ease-in-out 0.15s infinite;"></span>
                <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:ptBounce 0.6s ease-in-out 0.3s infinite;"></span>
            </div>
            <p style="font-size:0.88rem;font-weight:700;color:#0F172A;margin:0 0 3px;">Keluar...</p>
            <p style="font-size:0.75rem;color:#94A3B8;margin:0;">Sampai jumpa lagi 👋</p>
        </div>
    </div>
    <style>
        @keyframes ptSpin    { to { transform: rotate(360deg); } }
        @keyframes ptSpinRev { to { transform: rotate(-360deg); } }
        @keyframes ptBounce  { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    </style>

    {{-- Release Modal --}}
    @if($latestUpdate && $latestUpdate->show_modal)
    <div id="release-modal" data-session-key="rel_modal_shown_{{ auth()->check() ? md5(auth()->id()) : 'guest' }}" style="display:none;position:fixed;inset:0;z-index:9998;align-items:center;justify-content:center;padding:16px;">
        <div onclick="closeReleaseModal()" style="position:absolute;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);"></div>
        <div id="release-modal-box" style="position:relative;width:100%;max-width:440px;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 32px 64px rgba(15,23,42,.4);animation:relSlideIn .35s cubic-bezier(.34,1.56,.64,1);">
            <div style="padding:20px 24px 18px;background:linear-gradient(135deg,#0F172A 0%,#1E3A8A 100%);color:#fff;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <span style="font-size:.7rem;font-weight:700;background:rgba(255,255,255,.15);padding:3px 10px;border-radius:20px;font-family:monospace;margin-bottom:6px;display:inline-block;">{{ $latestUpdate->version }}</span>
                    <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:3px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;vertical-align:middle;margin-left:8px;" class="rel-badge-{{ $latestUpdate->type }}">
                        @php $relIcon = match($latestUpdate->type) { 'feature' => 'star', 'fix' => 'wrench', 'hotfix' => 'flame', default => 'git-commit' }; @endphp
                        <i data-lucide="{{ $relIcon }}" class="w-3 h-3"></i>
                        {{ ucfirst($latestUpdate->type) }}
                    </div>
                </div>
                <button onclick="closeReleaseModal()" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;flex-shrink:0;"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <div style="padding:22px 24px 24px;">
                <h2 style="font-size:1.05rem;font-weight:800;color:#0F172A;margin:0 0 14px;line-height:1.4;">{{ $latestUpdate->title }}</h2>
                <div style="font-size:.85rem;color:#475569;line-height:1.75;margin-bottom:18px;white-space:pre-wrap;word-break:break-word;font-family:inherit;">{!! nl2br(e($latestUpdate->content)) !!}</div>
                <button onclick="closeReleaseModal()" style="width:100%;padding:12px;border:none;border-radius:14px;background:linear-gradient(135deg,#0F172A,#1E3A8A);color:#fff;font-size:.9rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .2s;">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Mengerti
                </button>
            </div>
        </div>
    </div>
    <style>
        @keyframes relSlideIn{from{opacity:0;transform:translateY(20px) scale(.96)}to{opacity:1;transform:none}}
        .rel-badge-feature{background:rgba(76,29,149,.15);color:#7c3aed;border:1px solid rgba(124,92,255,.25)}
        .rel-badge-update{background:rgba(3,105,161,.12);color:#0369a1;border:1px solid rgba(3,105,161,.25)}
        .rel-badge-fix{background:rgba(21,128,61,.12);color:#15803d;border:1px solid rgba(21,128,61,.25)}
        .rel-badge-hotfix{background:rgba(220,38,38,.12);color:#dc2626;border:1px solid rgba(220,38,38,.25)}
        html.dark .rel-badge-feature{background:rgba(76,29,149,.2);color:#a78bfa;border-color:rgba(124,92,255,.3)}
        html.dark .rel-badge-update{background:rgba(3,105,161,.2);color:#38bdf8;border-color:rgba(56,189,248,.3)}
        html.dark .rel-badge-fix{background:rgba(21,128,61,.2);color:#4ade80;border-color:rgba(74,222,128,.3)}
        html.dark .rel-badge-hotfix{background:rgba(220,38,38,.2);color:#f87171;border-color:rgba(248,113,113,.3)}
    </style>
    <script>
    function closeReleaseModal() {
        var m = document.getElementById('release-modal');
        if (m) { m.style.transition = 'opacity .2s'; m.style.opacity = '0'; setTimeout(function(){ m.style.display = 'none'; }, 200); }
    }
    (function(){
        var m = document.getElementById('release-modal');
        if (!m) return;
        var KEY = m.dataset.sessionKey;
        if (sessionStorage.getItem(KEY)) return;
        function show() {
            m.style.display = 'flex';
            requestAnimationFrame(function() { m.style.opacity = '1'; });
            if (window.lucide) lucide.createIcons();
            try { sessionStorage.setItem(KEY, '1'); } catch(e) {}
        }
        setTimeout(show, 600);
    })();
    </script>
    @endif

    <script>
    (function() {
        var logoutForms = document.querySelectorAll('form[action*="logout"]');
        logoutForms.forEach(function(form) {
            form.addEventListener('submit', function() {
                var overlay = document.getElementById('logout-overlay');
                if (overlay) overlay.style.display = 'flex';
            });
        });
    })();
    </script>

    @include('partials.pwa-install-prompt')
</body>
</html>
