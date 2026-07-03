<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICB CT') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.7/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.4s ease-out; }
        .slide-up { animation: slideUp 0.4s ease-out; }
        .slide-in-left { animation: slideInLeft 0.3s ease-out; }
        .scale-in { animation: scaleIn 0.2s ease-out; }
        .bounce-in { animation: bounceIn 0.5s ease-out; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes bounceIn { 
            0% { opacity: 0; transform: scale(0.3); }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        /* Hover Glow Effect */
        .hover-glow { transition: all 0.3s ease; }
        .hover-glow:hover { 
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
            transform: translateY(-2px);
        }
        
        /* Nav Item Animation */
        .nav-item { position: relative; overflow: hidden; }
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .nav-item:hover::before { left: 100%; }
        
        /* ==========================================
           PULSE DOT ANIMATION - FIXED & PERMANENT
           ========================================== */
        .pulse-dot {
            display: inline-block !important;
            animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;
            transform-origin: center !important;
            will-change: transform, opacity !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        @keyframes pulse-green {
            0% {
                transform: scale(1);
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            50% {
                transform: scale(1.15);
                opacity: 0.85;
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
            }
            100% {
                transform: scale(1);
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
        
        /* Dropdown Animation */
        .dropdown-enter { animation: dropdownIn 0.25s ease-out; }
        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        /* Card Hover Effect */
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Button Ripple Effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 0; height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn-ripple:active::after {
            width: 300px; height: 300px;
        }

        /* ==========================================
           ICON CLICK ANIMATION - NEW
           ========================================== */
        .icon-click {
            transition: transform 0.15s ease !important;
        }
        
        .icon-click:active {
            transform: scale(0.92) !important;
        }

        /* Rotation Animation */
        .rotate-center {
            animation: rotate-center 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        @keyframes rotate-center {
            0% { transform: rotate(0); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="h-full bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-slate-100 transition-colors duration-300"
      x-data="{ 
          sidebarOpen: false, 
          darkMode: (() => { 
              try { 
                  return localStorage.getItem('theme') === 'dark' || 
                         (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
              } catch(e) { return false; } 
          })(),
          profileOpen: false,
          spinAnim: false
      }"
      :class="{ 'dark': darkMode }"
      x-init="
          $watch('darkMode', val => {
              try { localStorage.setItem('theme', val ? 'dark' : 'light'); } catch(e) {}
              if (val) document.documentElement.classList.add('dark');
              else document.documentElement.classList.remove('dark');
          });
          if (darkMode) document.documentElement.classList.add('dark');
          Alpine.store('toast', {
              items: [],
              show(message, type = 'success') {
                  const id = Date.now();
                  this.items.push({ id, message, type });
                  setTimeout(() => this.items = this.items.filter(i => i.id !== id), 5000);
              }
          });
          void 0;
      ">



    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm">
    </div>

    <!-- ========================================== -->
    <!-- SIDEBAR (Optimized Size)                   -->
    <!-- ========================================== -->
    <aside id="sidebar" 
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 left-0 z-50 flex h-screen w-64 flex-col bg-white dark:bg-navy-900 border-r border-slate-200 dark:border-slate-800 
                  transition-transform duration-300 ease-out">
        
        @php
            $appSettings = \App\Models\AppSetting::getInstance();
        @endphp

        <!-- Logo -->
        <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-200 dark:border-slate-800">
            @if($appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" class="w-9 h-9 object-contain">
            @else
                <div class="w-9 h-9 bg-gradient-to-br from-gold-400 to-gold-500 rounded-lg flex items-center justify-center shadow-lg shadow-gold-400/30 hover-glow">
                    <i data-lucide="layers" class="w-5 h-5 text-navy-900"></i>
                </div>
            @endif
            <div>
                <h1 class="font-bold text-base text-navy-800 dark:text-white leading-tight">
                    {{ $appSettings->app_name ?? 'ICB CT' }}
                </h1>
                <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ Auth::user()->isAdmin() ? 'Presensi Guru' : 'Portal Guru' }}</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @include('layouts.partials.sidebar-nav')
        </nav>

    </aside>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                               -->
    <!-- ========================================== -->
    <div class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300">
        
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white/80 dark:bg-navy-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-5 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors icon-click">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                
                <div>
                    <h2 class="text-base font-bold text-navy-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" id="realtime-clock">Memuat...</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode; spinAnim = true; setTimeout(() => spinAnim = false, 500)" 
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 group cursor-pointer focus:outline-none">
                    <i data-lucide="sun" x-show="!darkMode" class="w-5 h-5 text-slate-600 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
                    <i data-lucide="moon" x-show="darkMode" x-cloak class="w-5 h-5 text-gold-400 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative" 
                     x-data="{ 
                        open: false, 
                        unreadCount: {{ Auth::user()->unreadNotifications->count() }},
                        markAsRead() {
                            if (this.unreadCount > 0) {
                                fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                }).then(() => {
                                    this.unreadCount = 0;
                                });
                            }
                            this.open = !this.open;
                        }
                     }" 
                     @click.outside="open = false">
                    <button @click="markAsRead()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 relative icon-click">
                        <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-300"></i>
                        <template x-if="unreadCount > 0">
                            <span :class="unreadCount > 1 ? '-top-1 -right-1 min-w-[18px] h-[18px] px-1' : 'top-1.5 right-1.5 w-2.5 h-2.5'" 
                                  class="absolute bg-red-500 text-white text-[9px] font-bold rounded-full border-2 border-white dark:border-navy-900 flex items-center justify-center animate-pulse">
                                <span x-text="unreadCount > 1 ? (unreadCount > 99 ? '99+' : unreadCount) : ''"></span>
                            </span>
                        </template>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         x-transition:enter="dropdown-enter"
                         x-cloak
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                        
                        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white">Notifikasi</h3>
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.clear') }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold hover:underline">Tandai Baca</button>
                                    </form>
                                @endif
                                <a href="{{ route('notifications.index') }}" class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold hover:underline">Lihat Semua</a>
                            </div>
                        </div>

                        <div class="max-h-[400px] overflow-y-auto">
                            @forelse(Auth::user()->notifications->take(5) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 
                                            {{ ($notification->data['type'] ?? '') === 'success' ? 'bg-green-100 text-green-600' : 
                                               (($notification->data['type'] ?? '') === 'error' ? 'bg-red-100 text-red-600' : 
                                               'bg-blue-100 text-blue-600') }}">
                                            <i data-lucide="{{ ($notification->data['type'] ?? '') === 'success' ? 'check-circle' : 
                                                             (($notification->data['type'] ?? '') === 'error' ? 'alert-circle' : 'bell') }}" class="w-4 h-4"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-navy-800 dark:text-slate-200 {{ $notification->read_at ? '' : 'font-bold' }}">
                                                {{ $notification->data['message'] }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->read_at)
                                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-1.5"></div>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i data-lucide="bell-off" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi baru</p>
                                </div>
                            @endforelse
                        </div>

                        @if(Auth::user()->notifications->count() > 5)
                            <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-xs font-semibold text-navy-800 dark:text-white bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Lihat {{ Auth::user()->notifications->count() - 5 }} Notifikasi Lainnya
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Profile Dropdown (With Photo) -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 pr-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-all hover:scale-105 icon-click">
                        <div class="relative">
                            <img src="{{ Auth::user()->photo_url }}" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                            <!-- PERMANENT GREEN DOT - FIXED -->
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-navy-900 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-[11px] font-semibold text-navy-800 dark:text-white leading-tight">{{ Auth::user()->name }}</p>
                            <div class="flex items-center">
                                <span class="text-[9px] text-green-600 dark:text-green-400 font-medium">
                                    {{ Auth::user()->isAdmin() ? 'Operator' : (Auth::user()->subject ?? 'Guru') }}
                                </span>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="dropdown-enter"
                         x-cloak
                         class="absolute right-0 mt-2 w-72 bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        
                        <!-- Profile Header -->
                        <div class="p-4 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-slate-700 dark:to-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="{{ Auth::user()->photo_url }}" 
                                         class="w-12 h-12 rounded-full object-cover border-2 border-gold-400/50 shadow-lg">
                                    <!-- PERMANENT GREEN DOT - FIXED -->
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-navy-800 dark:border-slate-700 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-300 truncate">{{ Auth::user()->email }}</p>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                                        <span class="text-[10px] text-green-400 font-medium">Online Sekarang</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all hover:pl-5">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    <span>Profile Saya</span>
                                </a>

                            @else
                                <a href="{{ route('teacher.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all hover:pl-5">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    <span>Profil Saya</span>
                                </a>
                            @endif

                            <hr class="my-2 border-slate-200 dark:border-slate-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all hover:pl-5">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-5 lg:p-6 overflow-x-hidden">
            @if (session('success') || session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2">
                @if (session('success'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl slide-up">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    <p class="text-sm text-green-800 dark:text-green-200">{{ is_array(session('success')) ? implode(' ', session('success')) : session('success') }}</p>
                </div>
                @endif

                @if (session('error'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl slide-up">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                    <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
                @endif
            </div>
            @endif

            @yield('content')
        </main>
    </div>


    <script>
        // Initialize icons (Lucide v1: pass full icon map from UMD bundle)
        function initIcons() {
            if (!window.lucide || typeof lucide.createIcons !== 'function') return;
            try {
                const opts = {};
                if (lucide.icons && typeof lucide.icons === 'object') {
                    opts.icons = lucide.icons;
                }
                lucide.createIcons(opts);
            } catch (e) {
                console.warn('Lucide icons:', e);
            }
        }

        // Clock function
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
        
        // Start clock
        updateClock();
        setInterval(updateClock, 1000);

        // Initialize on DOM ready (after deferred lucide.min.js)
        document.addEventListener('DOMContentLoaded', function() {
            initIcons();
            requestAnimationFrame(function() { initIcons(); });
            
            // Add click animation to all icon buttons
            const iconButtons = document.querySelectorAll('.icon-click');
            iconButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.92)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });

        // Reinitialize icons after Alpine
        document.addEventListener('alpine:initialized', () => {
            initIcons();
        });
    </script>
</body>
</html>