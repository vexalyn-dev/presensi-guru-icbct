<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dev Panel · ICB CT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafafa;
            color: #111827;
        }
        
        /* Smooth fade transition for sections */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sidebar Item */
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin: 4px 16px;
            border-radius: 8px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .sidebar-item:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .sidebar-item.active {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: 600;
        }
        .sidebar-item.active svg {
            color: #111827;
        }

        /* Modern Card */
        .saas-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* SaaS Input */
        .saas-input {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            color: #111827;
            font-size: 14px;
            border-radius: 8px;
            padding: 8px 12px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .saas-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Button */
        .saas-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #111827;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .saas-btn:hover {
            background-color: #374151;
        }
        .saas-btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border-color: #d1d5db;
        }
        .saas-btn-secondary:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="antialiased flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 flex flex-col">
        {{-- Header / Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-md flex items-center justify-center shadow-sm">
                    <i data-lucide="command" class="w-4 h-4 text-white"></i>
                </div>
                <span class="font-semibold text-gray-900 tracking-tight">Dev Panel</span>
            </div>
        </div>

        {{-- Nav Links --}}
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Overview</div>
            <a onclick="switchTab('dashboard')" id="nav-dashboard" class="sidebar-item active">
                <i data-lucide="layout-grid" class="w-4 h-4"></i> Dashboard
            </a>

            <div class="px-4 mt-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</div>
            <a onclick="switchTab('apk')" id="nav-apk" class="sidebar-item">
                <i data-lucide="smartphone" class="w-4 h-4"></i> APK Manager
            </a>
            <a onclick="switchTab('system')" id="nav-system" class="sidebar-item">
                <i data-lucide="shield-alert" class="w-4 h-4"></i> System State
            </a>
            <a onclick="switchTab('releases')" id="nav-releases" class="sidebar-item">
                <i data-lucide="git-merge" class="w-4 h-4"></i> Releases
            </a>

            <div class="px-4 mt-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Links</div>
            <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" class="sidebar-item">
                <i data-lucide="github" class="w-4 h-4"></i> Repository
            </a>
            <a href="{{ url('/dashboard') }}" class="sidebar-item">
                <i data-lucide="external-link" class="w-4 h-4"></i> Main App
            </a>
        </div>

        {{-- Footer Profile --}}
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=6366f1&color=fff" alt="User" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">Vio Atmajaya</p>
                    <p class="text-xs text-gray-500 truncate">Developer</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="ml-64 flex-1 flex flex-col min-h-screen">
        
        {{-- Top Header --}}
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20">
            <h1 class="text-lg font-semibold text-gray-900" id="header-title">Dashboard</h1>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Production
                </div>
                <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}" class="text-gray-400 hover:text-gray-900 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </header>

        {{-- Scrollable Page Content --}}
        <div class="flex-1 p-8">
            {{-- Alerts --}}
            @if(session('success') || session('error'))
            <div class="mb-6">
                @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3 text-sm">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3 text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                    {{ session('error') }}
                </div>
                @endif
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        function initIcons() {
            if (window.lucide) lucide.createIcons();
        }

        const tabs = ['dashboard', 'apk', 'system', 'releases'];
        const titles = {
            'dashboard': 'Overview',
            'apk': 'APK Manager',
            'system': 'System State',
            'releases': 'Release History'
        };

        function switchTab(tabId) {
            tabs.forEach(t => {
                // Handle Content
                const contentEl = document.getElementById('tab-' + t);
                if(contentEl) {
                    if (t === tabId) {
                        contentEl.classList.add('active');
                    } else {
                        contentEl.classList.remove('active');
                    }
                }
                
                // Handle Nav Links
                const navEl = document.getElementById('nav-' + t);
                if(navEl) {
                    if (t === tabId) {
                        navEl.classList.add('active');
                    } else {
                        navEl.classList.remove('active');
                    }
                }
            });

            // Update Header Title
            const titleEl = document.getElementById('header-title');
            if (titleEl) {
                titleEl.textContent = titles[tabId] || 'Dashboard';
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initIcons();
            // Start at dashboard
            switchTab('dashboard');
        });
    </script>
    @yield('scripts')
</body>
</html>
