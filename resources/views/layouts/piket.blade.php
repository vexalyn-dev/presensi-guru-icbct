<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

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
               class="fixed inset-y-0 left-0 z-50 flex h-full min-h-screen w-64 flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transition-transform duration-300 ease-in-out lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Logo -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                    @if($appSettings->app_logo)
                        <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="school" class="w-5 h-5 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-sm font-bold text-navy-800 dark:text-white">{{ $appSettings->app_name ?? 'ICB CINTA TEKNIKA' }}</h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Portal Guru Piket</p>
                    </div>
                </div>
            </div>

            <!-- Menu Guru Piket -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">

                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>

                <a href="{{ route('piket.dashboard') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.dashboard') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-4">Presensi</p>

                <a href="{{ route('piket.attendance') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.attendance*') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Presensi Harian</span>
                </a>

                <a href="{{ route('piket.class-attendance.manual') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.class-attendance*') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="clipboard-edit" class="w-4 h-4"></i>
                    <span>Manual Presensi</span>
                </a>

                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-4">Administrasi</p>

                <a href="{{ route('piket.leave-approval') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.leave-approval*') || request()->routeIs('piket.leaves*') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="file-check" class="w-4 h-4"></i>
                    <span>Izin &amp; Sakit</span>
                </a>

                <a href="{{ route('piket.holidays') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.holidays*') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="calendar-off" class="w-4 h-4"></i>
                    <span>Kalender Libur</span>
                </a>

                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-4">Lainnya</p>

                <a href="{{ route('piket.support') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('piket.support*') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="life-buoy" class="w-4 h-4"></i>
                    <span>Pusat Bantuan</span>
                </a>

                <a href="{{ route('download-apk') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('download-apk') ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                    <span>Download APK</span>
                </a>

            </nav>
        </aside>

           <!-- Notification config (used by external JS) -->
           <div id="laravel-config" style="display:none;"
               data-unread-url="{{ route('piket.notifications.api.unread') }}"
               data-user-id="{{ auth()->id() ?? '' }}"></div>

           <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen min-w-0">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-20">
                <div class="px-3 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                        <!-- Hamburger Menu (Mobile Only) -->
                        <button @click="sidebarOpen = true" 
                                class="lg:hidden p-1.5 sm:p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors flex-shrink-0">
                            <i data-lucide="menu" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                        </button>
                        
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white truncate">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} pukul {{ now()->format('H.i') }}</p>
                            @yield('topbar-reminder')
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
                        <!-- Dark/Light Mode Toggle -->
                        <button onclick="toggleDarkMode()" class="p-1.5 sm:p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Toggle Dark Mode">
                            <i data-lucide="sun" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-600 dark:text-slate-400 hidden dark:block"></i>
                            <i data-lucide="moon" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-600 dark:text-slate-400 block dark:hidden"></i>
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
                                <img src="{{ auth()->user()->photo_url }}" 
                                     class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600 flex-shrink-0"
                                     onerror="this.src='{{ asset('images/default-operator.png') }}'">
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white truncate max-w-[120px]">{{ auth()->user()->name }}</p>
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
                                        <img src="{{ auth()->user()->photo_url }}"
                                             class="w-10 h-10 rounded-full object-cover border-2 border-white/30 flex-shrink-0"
                                             onerror="this.src='{{ asset('images/default-operator.png') }}'">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
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
                @yield('content')
                
                <!-- Spacer for Bottom Nav (Mobile Only) -->
                <div class="lg:hidden h-32"></div>
            </div>
        </main>
        
        <!-- Mobile Overlay (tidak digunakan lagi karena sidebar hidden di mobile) -->
    </div>

    @php
        $autoLogoutVal = \App\Models\Setting::get('auto_logout', 'off');
        $autoLogoutMinutes = ($autoLogoutVal !== 'off' && is_numeric($autoLogoutVal)) ? (int) $autoLogoutVal : 0;
    @endphp
    @if($autoLogoutMinutes > 0)
    <script>
        (function() {
            const timeoutMs = {{ $autoLogoutMinutes }} * 60 * 1000;
            let logoutTimer;
            function resetLogoutTimer() {
                clearTimeout(logoutTimer);
                logoutTimer = setTimeout(() => {
                    alert('Sesi Anda telah berakhir karena tidak ada aktivitas selama {{ $autoLogoutMinutes }} menit.');
                    const logoutForm = document.querySelector('form[action*="logout"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.href = "{{ route('login') }}";
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
</body>
</html>
