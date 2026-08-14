@extends('layouts.developer')
@section('page-title', 'Dashboard Overview')
@section('content')

{{-- ═══ SECTION: DASHBOARD ═══ --}}
<div id="sec-dashboard" class="space-y-6 sm:space-y-8 fade-in-up">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-[#0A0F24] p-8 sm:p-12 shadow-2xl">
        {{-- Animated Background Orbs --}}
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-purple-600/30 rounded-full blur-[80px] float-anim mix-blend-screen"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-blue-600/30 rounded-full blur-[80px] float-anim mix-blend-screen" style="animation-delay: -3s;"></div>
        
        {{-- Subtle Grid Pattern --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 backdrop-blur-md mb-6">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-300 tracking-widest uppercase">System Online</span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-outfit font-bold text-white mb-4 leading-tight">
                    Welcome back, <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">Vio Atmajaya</span>
                </h1>
                
                <p class="text-sm sm:text-base text-slate-400 leading-relaxed max-w-md">
                    Manage ICB CT ecosystem, oversee application deployments, and monitor system health from your centralized command center.
                </p>
                
                <div class="mt-8 flex items-center gap-4">
                    <button onclick="devShowSection('sec-updates')" class="px-6 py-3 rounded-xl bg-white text-slate-900 font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-lg shadow-white/10">
                        View Releases
                    </button>
                    <a href="https://vexalyndev.my.id" target="_blank" class="px-6 py-3 rounded-xl bg-white/10 text-white font-bold text-sm hover:bg-white/20 backdrop-blur-md border border-white/10 hover:scale-105 active:scale-95 transition-all">
                        Developer Profile
                    </a>
                </div>
            </div>

            {{-- Metric Cards inside Banner --}}
            <div class="grid grid-cols-2 gap-4 w-full md:w-auto flex-shrink-0">
                <div class="glass-card !bg-white/5 !border-white/10 rounded-2xl p-5 hover:!bg-white/10 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <p class="text-3xl font-outfit font-bold text-white mb-1">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-slate-400">Total Registered Users</p>
                </div>
                <div class="glass-card !bg-white/5 !border-white/10 rounded-2xl p-5 hover:!bg-white/10 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-outfit font-bold text-white mb-1">{{ $stats['total_teachers'] }}</p>
                    <p class="text-xs text-slate-400">Active Teachers</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        {{-- System Info --}}
        <div class="glass-card rounded-[2rem] p-6 sm:p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <i data-lucide="cpu" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-outfit font-bold text-slate-800 dark:text-white">System Information</h3>
                </div>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach([
                    ['Framework', 'Laravel', $stats['laravel_version'], 'text-red-500'],
                    ['Runtime', 'PHP Engine', $stats['php_version'], 'text-indigo-500'],
                    ['Environment', 'State', strtoupper($stats['env']), 'text-emerald-500'],
                    ['Debug Mode', 'Config', $stats['debug']?'Enabled':'Disabled', $stats['debug']?'text-amber-500':'text-slate-500'],
                    ['Timezone', 'Location', 'WIB', 'text-blue-500'],
                    ['Host', 'Server', parse_url($stats['app_url'],PHP_URL_HOST)??'-', 'text-purple-500'],
                ] as [$label, $sub, $val, $col])
                <div class="p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200/50 dark:border-slate-700/50 hover:border-purple-300 dark:hover:border-purple-500/50 transition-colors group">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $label }}</p>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 truncate">{{ $val }}</p>
                    <div class="w-full h-1 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-current {{ $col }} w-1/2 group-hover:w-full transition-all duration-500"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="glass-card rounded-[2rem] p-6 sm:p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-outfit font-bold text-slate-800 dark:text-white">Quick Actions</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('developer.clear-cache',$secret) }}" onclick="return confirm('Clear application cache?')"
                   class="flex flex-col p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 hover:border-teal-400 hover:shadow-lg hover:shadow-teal-500/10 hover:-translate-y-1 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Clear Cache</span>
                    <span class="text-[10px] text-slate-500 mt-1">Flush views & config</span>
                </a>
                
                <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank"
                   class="flex flex-col p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 hover:border-blue-400 hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-1 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Run Migrate</span>
                    <span class="text-[10px] text-slate-500 mt-1">Execute DB migrations</span>
                </a>

                <a href="{{ url('/dashboard') }}"
                   class="flex flex-col p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 hover:border-purple-400 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Main Dashboard</span>
                    <span class="text-[10px] text-slate-500 mt-1">Enter main application</span>
                </a>

                <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank"
                   class="flex flex-col p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 hover:border-slate-400 hover:shadow-lg hover:-translate-y-1 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Repository</span>
                    <span class="text-[10px] text-slate-500 mt-1">Source code on GitHub</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECTION: APK ═══ --}}
<div id="sec-apk" class="hidden space-y-6 sm:space-y-8 fade-in-up">
    
    <div class="glass-card rounded-[2rem] p-6 sm:p-10">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                <i data-lucide="smartphone" class="w-7 h-7 text-white"></i>
            </div>
            <div>
                <h2 class="text-2xl font-outfit font-bold text-slate-800 dark:text-white">Application Packages</h2>
                <p class="text-sm text-slate-500">Distribute Android APKs directly to users.</p>
            </div>
        </div>

        @if($appSetting?->apk_file)
        <div class="flex flex-col sm:flex-row sm:items-center gap-6 p-6 rounded-3xl bg-emerald-50/50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 mb-8 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400/0 via-emerald-400/10 to-emerald-400/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
            
            <div class="w-16 h-16 bg-white dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center shadow-sm flex-shrink-0">
                <i data-lucide="package-check" class="w-8 h-8 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <h3 class="text-lg font-bold text-emerald-900 dark:text-emerald-100 truncate">{{ $appSetting->apk_name ?? 'ICB CT Presensi' }}</h3>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-200 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold tracking-widest">v{{ $appSetting->apk_version_label ?? '1.0.0' }}</span>
                </div>
                <div class="flex items-center gap-4 text-xs text-emerald-700/80 dark:text-emerald-400/80">
                    <span class="flex items-center gap-1.5"><i data-lucide="hard-drive" class="w-3.5 h-3.5"></i> {{ $appSetting->apk_size_human ?? '-' }}</span>
                    @if($appSetting->apk_uploaded_at)
                        <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $appSetting->apk_uploaded_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            
            <form action="{{ route('developer.apk.delete',$secret) }}" method="POST" onsubmit="return confirm('Remove this APK?')" class="sm:ml-auto">
                @csrf @method('DELETE')
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white dark:bg-slate-800 text-red-600 dark:text-red-400 font-bold text-sm hover:shadow-lg hover:scale-105 active:scale-95 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Remove Build
                </button>
            </form>
        </div>
        @else
        <div class="flex flex-col items-center justify-center p-10 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 mb-8 bg-slate-50/50 dark:bg-slate-800/30">
            <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center mb-4">
                <i data-lucide="package-x" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">No APK published</p>
            <p class="text-xs text-slate-500 mt-1">Upload a build below to make it available to users.</p>
        </div>
        @endif

        <form action="{{ route('developer.apk',$secret) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="mb-2">
                <h3 class="text-base font-outfit font-bold text-slate-800 dark:text-white mb-1">Upload New Build</h3>
                <p class="text-xs text-slate-500">Provide the .apk file and version metadata.</p>
            </div>

            <div id="apkZone" onclick="document.getElementById('apkFile').click()"
                 class="relative overflow-hidden rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-500/10 p-12 text-center transition-all cursor-pointer group"
                 ondragover="event.preventDefault();this.classList.add('border-purple-500','bg-purple-50','dark:bg-purple-500/10')"
                 ondragleave="this.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-500/10')"
                 ondrop="event.preventDefault();this.classList.remove('border-purple-500');handleApk(event.dataTransfer.files[0])">
                
                <input type="file" name="apk_file" accept=".apk" id="apkFile" class="hidden" onchange="handleApk(this.files[0])">
                
                <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform group-hover:bg-purple-100 dark:group-hover:bg-purple-500/20">
                    <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors"></i>
                </div>
                
                <p class="text-base font-bold text-slate-700 dark:text-slate-200 mb-1" id="apkZoneTxt">Drop APK here or click to browse</p>
                <p class="text-xs font-medium text-slate-500">Supports .apk formats up to 100MB</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach([
                    ['apk_name','apkName','box','App Name','ICB CT Mobile'],
                    ['apk_version','apkVer','tag','Version Number','1.0.0'],
                    ['apk_min_android','apkAndroid','smartphone','Min. SDK Req.','Android 8.0+'],
                    ['apk_changelog','apkLog','file-text','Release Notes','Bug fixes and improvements'],
                ] as [$name, $id, $icon, $label, $placeholder])
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">{{ $label }}</label>
                    <div class="relative group">
                        <i data-lucide="{{ $icon }}" class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 group-focus-within:text-purple-600 transition-colors pointer-events-none"></i>
                        <input type="text" name="{{ $name }}" id="{{ $id }}" placeholder="{{ $placeholder }}" value="{{ old($name, $appSetting?->$name ?? '') }}"
                               class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all shadow-sm">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pt-4 flex items-center gap-4">
                <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-lg">
                    <i data-lucide="upload" class="w-4 h-4"></i> Publish Build
                </button>
                @if($appSetting?->apk_url)
                <a href="{{ $appSetting->apk_url }}" target="_blank"
                   class="flex items-center gap-2 px-6 py-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i> Download Current
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ═══ SECTION: MAINTENANCE ═══ --}}
<div id="sec-maint" class="hidden space-y-6 sm:space-y-8 fade-in-up">
    
    <div class="glass-card rounded-[2rem] p-6 sm:p-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <i data-lucide="shield-alert" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-outfit font-bold text-slate-800 dark:text-white">System State</h2>
                    <p class="text-sm text-slate-500">Toggle application availability for standard users.</p>
                </div>
            </div>
            
            @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
            <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $mOn ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' }}">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $mOn ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 {{ $mOn ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                </span>
                <span class="text-xs font-bold tracking-widest uppercase">{{ $mOn ? 'Maintenance Active' : 'System Normal' }}</span>
            </div>
        </div>

        <form action="{{ route('developer.maintenance',$secret) }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 p-6 sm:p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="max-w-md">
                    <h3 class="text-lg font-outfit font-bold text-slate-800 dark:text-white mb-2">Maintenance Mode</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        When active, standard users will see a maintenance page. <strong class="text-purple-600 dark:text-purple-400">Developers & Admins</strong> will retain full access to the system.
                    </p>
                </div>
                
                <label class="relative inline-flex items-center cursor-pointer transform scale-150 sm:mr-4" onclick="toggleMaintUI()">
                    <input type="hidden" name="maintenance_mode" id="maintVal" value="{{ $mOn?'1':'0' }}">
                    <div id="maintTrack" class="w-11 h-6 rounded-full transition-colors duration-300 {{ $mOn?'bg-amber-500':'bg-slate-300 dark:bg-slate-600' }}"></div>
                    <div id="maintThumb" class="absolute top-[2px] left-[2px] bg-white rounded-full h-5 w-5 transition-transform duration-300 shadow-md {{ $mOn?'translate-x-full':'' }}"></div>
                </label>
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Public Message</label>
                <div class="relative">
                    <i data-lucide="message-square" class="absolute left-4 top-4 w-5 h-5 text-slate-400 pointer-events-none"></i>
                    <textarea name="maintenance_message" rows="3" placeholder="We are currently upgrading the system..."
                              class="w-full pl-12 pr-4 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all shadow-sm resize-none">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
                </div>
            </div>
            
            <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-lg">
                <i data-lucide="save" class="w-4 h-4"></i> Apply Configuration
            </button>
        </form>
    </div>
</div>

{{-- ═══ SECTION: UPDATES ═══ --}}
<div id="sec-updates" class="hidden space-y-6 sm:space-y-8 fade-in-up">
    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 sm:gap-8">
        
        {{-- Create Form --}}
        <div class="xl:col-span-1 glass-card rounded-[2rem] p-6 sm:p-8 h-fit sticky top-28">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i data-lucide="rocket" class="w-6 h-6 text-white"></i>
                </div>
                <h2 class="text-xl font-outfit font-bold text-slate-800 dark:text-white">New Release</h2>
            </div>

            <form action="{{ route('developer.updates.store',$secret) }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Version</label>
                        <input type="text" name="version" placeholder="2.0.0" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Type</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all cursor-pointer appearance-none">
                            <option value="feature">✨ Feature</option>
                            <option value="update">⬆️ Update</option>
                            <option value="fix">🔧 Fix</option>
                            <option value="hotfix">🚨 Hotfix</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Title</label>
                    <input type="text" name="title" placeholder="Massive UI Redesign" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all" required>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Changelog</label>
                    <textarea name="content" rows="4" placeholder="- Added glassmorphism&#10;- Improved animations" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all resize-none" required></textarea>
                </div>
                
                <div class="pt-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-purple-300 dark:hover:border-purple-600 transition-colors">
                        <input type="checkbox" name="show_modal" value="1" checked class="w-5 h-5 rounded border-slate-300 text-purple-600 focus:ring-purple-600 bg-white dark:bg-slate-900">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">Show Modal</span>
                            <span class="text-[10px] text-slate-500">Alert users on next login</span>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-lg mt-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Publish Release
                </button>
            </form>
        </div>

        {{-- History List --}}
        <div class="xl:col-span-2 space-y-4">
            @if(count($updates) > 0)
                @foreach($updates as $u)
                @php 
                    $cfg = match($u->type) {
                        'feature' => ['bg-purple-100 dark:bg-purple-500/20','text-purple-700 dark:text-purple-300','border-purple-200 dark:border-purple-500/30','star'],
                        'fix'     => ['bg-emerald-100 dark:bg-emerald-500/20','text-emerald-700 dark:text-emerald-300','border-emerald-200 dark:border-emerald-500/30','wrench'],
                        'hotfix'  => ['bg-red-100 dark:bg-red-500/20','text-red-700 dark:text-red-300','border-red-200 dark:border-red-500/30','flame'],
                        default   => ['bg-blue-100 dark:bg-blue-500/20','text-blue-700 dark:text-blue-300','border-blue-200 dark:border-blue-500/30','arrow-up-circle'],
                    }; 
                @endphp
                <div class="glass-card p-5 sm:p-6 rounded-[1.5rem] flex flex-col sm:flex-row gap-5 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex-shrink-0 flex sm:flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $cfg[0] }} {{ $cfg[1] }} border {{ $cfg[2] }} flex items-center justify-center">
                            <i data-lucide="{{ $cfg[3] }}" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-400">{{ $u->created_at->format('M d') }}</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <h3 class="text-lg font-outfit font-bold text-slate-800 dark:text-white">{{ $u->title }}</h3>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-widest uppercase {{ $cfg[0] }} {{ $cfg[1] }}">v{{ $u->version }}</span>
                            @if($u->show_modal)
                                <span class="px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 text-[9px] font-bold text-slate-500 uppercase tracking-widest"><i data-lucide="bell" class="w-3 h-3 inline mr-1"></i>Modal</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 whitespace-pre-line leading-relaxed">{{ $u->content }}</p>
                    </div>

                    <form action="{{ route('developer.updates.delete',[$secret,$u->id]) }}" method="POST" onsubmit="return confirm('Delete this release log?')" class="flex-shrink-0 self-start sm:self-center ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            @else
                <div class="glass-card rounded-[2rem] p-16 flex flex-col items-center justify-center text-center h-full">
                    <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <h3 class="text-xl font-outfit font-bold text-slate-800 dark:text-white mb-2">No Releases Yet</h3>
                    <p class="text-sm text-slate-500">Publish your first update to notify users.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
