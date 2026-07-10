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
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Hide scrollbar visually but keep scroll functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;             /* Chrome, Safari, Opera */
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen">
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
    <div class="flex min-h-screen">
        
        <!-- Sidebar (Desktop only - hidden on mobile) -->
        <aside id="sidebar" class="hidden lg:flex w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex-col fixed h-full z-30">
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
                        <h1 class="text-sm font-bold text-navy-800 dark:text-white">ICB CINTA TEKNIKA</h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Portal Guru</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.dashboard') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('teacher.schedule') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.schedule') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    <span>Jadwal Mengajar</span>
                </a>

                <a href="{{ route('teacher.attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Presensi Harian</span>
                </a>

                <a href="{{ route('teacher.class-attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.class-attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan" class="w-4 h-4"></i>
                    <span>Presensi Kelas</span>
                </a>

                <a href="{{ route('teacher.history', ['type' => 'daily']) }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.history') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('teacher.leave') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.leave*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Izin/Sakit</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-4 sm:px-6 py-4 sticky top-0 z-20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} pukul {{ now()->format('H.i') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- Dark/Light Mode Toggle -->
                        <button onclick="toggleDarkMode()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Toggle Dark Mode">
                            <i data-lucide="sun" class="w-5 h-5 text-slate-600 dark:text-slate-400 hidden dark:block"></i>
                            <i data-lucide="moon" class="w-5 h-5 text-slate-600 dark:text-slate-400 block dark:hidden"></i>
                        </button>

                        <!-- Notifikasi Dropdown -->
                        <div class="relative" x-data="notificationDropdown()" 
                        @click.outside="open = false"
                        x-init="init()">
                            
                            <button @click="open = !open" class="relative p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                                @if(auth()->user()->unreadCount() > 0)
                                <span class="notification-badge absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
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
                                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50 max-h-[480px] overflow-y-auto scrollbar-hide"
                                 x-cloak>
                                
                                <!-- Header -->
                                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">Notifikasi</h3>
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse(auth()->user()->notifications()->take(5)->get() as $notif)
                                    <a href="{{ $notif->action_url ?? '#' }}" 
                                       class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $notif->color }} flex items-center justify-center flex-shrink-0">
                                                <i data-lucide="{{ $notif->icon }}" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $notif->title }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ $notif->time_ago }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="p-8 text-center">
                                        <i data-lucide="bell-off" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi</p>
                                    </div>
                                    @endforelse
                                </div>
                                
                                <!-- Footer -->
                                <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-center">
                                    <a href="{{ route('teacher.notifications') }}" class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-3 p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff' }}" 
                                     class="w-9 h-9 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600">
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white truncate max-w-[150px]">{{ auth()->user()->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Guru</span>
                                        @if($teacherSubject)
                                        <span class="px-1.5 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[9px] font-bold">
                                            {{ $teacherSubject }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50"
                                 x-cloak>
                                
                                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-sm font-bold text-navy-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Guru</span>
                                        @if($teacherSubject)
                                        <span class="px-2 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[10px] font-bold">
                                            {{ $teacherSubject }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="py-1">
                                    <a href="{{ route('teacher.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                        <span>Profil Saya</span>
                                    </a>
                                </div>
                                
                                <div class="border-t border-slate-200 dark:border-slate-700 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
        
        <!-- Mobile Overlay (tidak digunakan lagi karena sidebar hidden di mobile) -->
    </div>

    <!-- Bottom Navigation (Mobile Only) - Animated Bubble -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50">
        <nav id="bottomNav" class="relative bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 shadow-2xl px-2 py-3">
            
            <!-- Animated Bubble Background -->
            <div id="navBubble" class="absolute -top-6 w-12 h-12 bg-white dark:bg-slate-800 rounded-full shadow-lg border-2 border-slate-100 dark:border-slate-700 transition-all pointer-events-none" 
                 style="left: 0; transform: translateX(-50%);">
            </div>
            
            <div class="grid grid-cols-6 gap-0 relative z-10">
                <!-- Dashboard -->
                <a href="{{ route('teacher.dashboard') }}" 
                   data-route="teacher.dashboard"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="layout-dashboard" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Dashboard</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>

                <!-- Jadwal -->
                <a href="{{ route('teacher.schedule') }}" 
                   data-route="teacher.schedule"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="calendar-range" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Jadwal</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>

                <!-- Absensi -->
                <a href="{{ route('teacher.attendance') }}" 
                   data-route="teacher.attendance"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="scan-line" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Absensi</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>

                <!-- Kelas -->
                <a href="{{ route('teacher.class-attendance') }}" 
                   data-route="teacher.class-attendance"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="scan" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Kelas</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('teacher.history', ['type' => 'daily']) }}" 
                   data-route="teacher.history"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="history" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Riwayat</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>

                <!-- Izin -->
                <a href="{{ route('teacher.leave') }}" 
                   data-route="teacher.leave"
                   class="nav-item relative flex flex-col items-center justify-end pb-3 pt-1 transition-colors duration-300">
                    <div class="flex items-center justify-center mb-1.5">
                        <i data-lucide="file-text" class="nav-icon w-6 h-6 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    </div>
                    <div class="h-7"></div>
                    <span class="nav-label text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">Izin</span>
                    <div class="nav-indicator absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full opacity-0 transition-opacity duration-300"></div>
                </a>
            </div>
        </nav>
    </div>

    <!-- Spacer untuk bottom nav -->
    <div class="lg:hidden h-24"></div>

    <script>
        // Bottom Navigation Bubble Animation Controller
        document.addEventListener('DOMContentLoaded', function() {
            const bubble = document.getElementById('navBubble');
            const navItems = document.querySelectorAll('.nav-item');
            const currentPath = window.location.pathname;
            
            // Fungsi untuk set active state
            function setActiveState(item, animate = false) {
                // Reset semua item
                navItems.forEach(navItem => {
                    const icon = navItem.querySelector('.nav-icon');
                    const label = navItem.querySelector('.nav-label');
                    const indicator = navItem.querySelector('.nav-indicator');
                    
                    icon.classList.remove('text-navy-800', 'dark:text-gold-400');
                    icon.classList.add('text-slate-400', 'dark:text-slate-500');
                    
                    label.classList.remove('text-navy-800', 'dark:text-gold-400');
                    label.classList.add('text-slate-400', 'dark:text-slate-500');
                    
                    indicator.classList.add('opacity-0');
                });
                
                // Set active item
                const icon = item.querySelector('.nav-icon');
                const label = item.querySelector('.nav-label');
                const indicator = item.querySelector('.nav-indicator');
                
                icon.classList.remove('text-slate-400', 'dark:text-slate-500');
                icon.classList.add('text-navy-800', 'dark:text-gold-400');
                
                label.classList.remove('text-slate-400', 'dark:text-slate-500');
                label.classList.add('text-navy-800', 'dark:text-gold-400');
                
                indicator.classList.remove('opacity-0');
                
                // Posisi bubble
                const itemRect = item.getBoundingClientRect();
                const navRect = document.getElementById('bottomNav').getBoundingClientRect();
                const newLeft = itemRect.left - navRect.left + (itemRect.width / 2);
                
                if (animate) {
                    // Animasi shrink-move-grow
                    bubble.style.transition = 'transform 150ms cubic-bezier(0.34, 1.56, 0.64, 1)';
                    bubble.style.transform = `translateX(-50%) scale(0)`;
                    
                    setTimeout(() => {
                        bubble.style.left = `${newLeft}px`;
                        bubble.style.transition = 'left 200ms ease-out, transform 150ms cubic-bezier(0.34, 1.56, 0.64, 1) 50ms';
                    }, 150);
                    
                    setTimeout(() => {
                        bubble.style.transform = `translateX(-50%) scale(1)`;
                    }, 200);
                } else {
                    // Langsung tanpa animasi (page load)
                    bubble.style.transition = 'none';
                    bubble.style.left = `${newLeft}px`;
                    bubble.style.transform = `translateX(-50%) scale(1)`;
                    setTimeout(() => {
                        bubble.style.transition = 'left 200ms ease-out, transform 150ms cubic-bezier(0.34, 1.56, 0.64, 1)';
                    }, 50);
                }
            }
            
            // Set initial active state based on current route
            let activeItem = null;
            navItems.forEach(item => {
                const route = item.getAttribute('data-route');
                if (currentPath.includes(route.replace('teacher.', '/teacher/'))) {
                    activeItem = item;
                }
            });
            
            if (activeItem) {
                setActiveState(activeItem, false);
            }
            
            // Handle click events
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetUrl = this.getAttribute('href');
                    
                    // Animate bubble
                    setActiveState(this, true);
                    
                    // Navigate after animation completes
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 320);
                });
            });
        });
    </script>

    <script>
        // Check saved theme on load — HARUS di atas segalanya
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Initialize Lucide icons
        function initIcons() {
            if (window.lucide) lucide.createIcons();
        }

        // Toggle Dark Mode
        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            initIcons();
        }

        // Mark notification as read
        function markAsRead(notificationId) {
            fetch(`/teacher/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    setTimeout(() => window.location.reload(), 400);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => initIcons());
        document.addEventListener('alpine:initialized', () => initIcons());
    </script>
    
    <!-- Alpine.js for dropdown -->
    <script>
        function notificationDropdown() {
            return {
                open: false,
                markRead() {
                    if (this.open) {
                        fetch('{{ route("teacher.notifications.read-all") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(response => {
                            if (response.ok) {
                                document.querySelectorAll('.notification-badge').forEach(el => el.remove());
                            }
                        });
                    }
                },
                init() {
                    this.$watch('open', value => this.markRead());
                }
            };
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>